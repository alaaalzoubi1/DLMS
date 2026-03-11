<?php

namespace App\Zatca\Services;

use App\Models\CreditNote;
use App\Models\CreditNoteItem;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\SubscriberZatcaCredential;
use App\Models\ZatcaDocument;
use Exception;
use Illuminate\Support\Facades\DB;

class CreditNoteService
{
    protected ZatcaInvoiceService $invoiceService;

    public function __construct(ZatcaInvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    public function createCreditNote(Order $order, array $items, string $reason = null): CreditNote
    {
        return DB::transaction(function () use ($order, $items, $reason) {

            $creditNote = CreditNote::create([
                'order_id' => $order->id,
                'total_amount' => $order->cost,
                'subscriber_id' => $order->subscriber_id,
                'reason' => $reason
            ]);

            foreach ($items as $item) {

                $orderProduct = OrderProduct::where('id', $item['order_product_id'])
                    ->where('order_id', $order->id)
                    ->firstOrFail();

                $this->validateReturnQuantity($orderProduct, $item['quantity']);

                CreditNoteItem::create([
                    'credit_note_id' => $creditNote->id,
                    'order_product_id' => $orderProduct->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $orderProduct->unit_price
                ]);
            }

            return $creditNote->load('items.orderProduct');
        });
    }

    protected function validateReturnQuantity(OrderProduct $product, int $requestedQty): void
    {
        $originalQty = count($product->tooth_numbers ?? []) ?: 1;

        $returnedQty = CreditNoteItem::where('order_product_id', $product->id)->sum('quantity');

        $available = $originalQty - $returnedQty;

        if ($requestedQty > $available) {
            throw new Exception("Return quantity exceeds available quantity");
        }
    }

    /**
     * @throws Exception
     */
    public function submitCreditNote(
        CreditNote $creditNote,
        SubscriberZatcaCredential $credentials
    ): ZatcaDocument {

        $order = $creditNote->order;

        $nextIcv = $credentials->last_icv + 1;
        $previousHash = $credentials->last_invoice_hash;

        $invoiceDocument = $this->getValidZatcaInvoice($order);

        $document = $this->invoiceService->buildCreditNoteDocument(
            $order,
            $creditNote,
            $invoiceDocument,
            $nextIcv,
            $previousHash
        );

        $response = $this->invoiceService->sendCreditNote(
            $document,
            $credentials
        );

        return $this->invoiceService->storeCreditNoteResponse(
            $creditNote,
            $document,
            $response,
            $credentials
        );
    }

    /**
     * @throws Exception
     */
    protected function getValidZatcaInvoice(Order $order): ZatcaDocument
    {
        $doc = ZatcaDocument::where('order_id', $order->id)
            ->where('invoice_type', 'TAX_INVOICE')
            ->whereIn('zatca_http_status', [200, 202])
            ->latest()
            ->first();

        if (!$doc) {
            throw new Exception(
                "Cannot create credit note. Order {$order->id} is not invoiced in ZATCA yet."
            );
        }

        return $doc;
    }
}
