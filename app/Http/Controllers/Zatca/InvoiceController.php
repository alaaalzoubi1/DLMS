<?php

namespace App\Http\Controllers\Zatca;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ZatcaDocument;
use App\Zatca\Services\CreditNoteService;
use App\Zatca\Services\ZatcaInvoiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    protected ZatcaInvoiceService $zatcaService;
    protected CreditNoteService $creditNoteService;

    public function __construct(ZatcaInvoiceService $zatcaService,CreditNoteService $creditNoteService)
    {
        $this->zatcaService = $zatcaService;
        $this->creditNoteService = $creditNoteService;
    }

    public function invoiceBulk(Request $request) : JsonResponse
    {
        $request->validate([
            'order_ids' => 'required|array|max:100',
            'order_ids.*' => 'exists:orders,id'
        ]);

        DB::beginTransaction();

        try {
            $orders = Order::with([
                'doctor.clinic.address',
                'subscriber.address',
                'products',
                'discount'
            ])
                ->whereIn('id', $request->order_ids)
                ->get();

            if ($orders->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No orders found'
                ]);
            }

            $subscriberId = auth('admin')->user()->subscriber_id;
            foreach ($orders as $order) {
                if ($order->subscriber_id !== $subscriberId) {
                    throw new \Exception('All orders must belong to the same subscriber.');
                }
            }

            $alreadyInvoiced = ZatcaDocument::whereIn('order_id', $orders->pluck('id'))
                ->where('invoice_type', 'TAX_INVOICE')
                ->whereIn('zatca_http_status', [200, 202])
                ->exists();

            if ($alreadyInvoiced) {
                throw new \Exception('One or more orders have already been invoiced successfully.');
            }

            $subscriber = $orders->first()->subscriber;

            $credentials = $subscriber->zatcaCredential()
                ->where('environment', config('services.zatca.environment'))
                ->first();

            if (!$credentials) {
                throw new \Exception('ZATCA credentials not found for this subscriber.');
            }

            $documents = $this->zatcaService->buildBulkPayload($orders, $credentials);

            $response = $this->zatcaService->sendBulkInvoice($documents, $credentials);

            $stored = $this->zatcaService->storeBulkResponse($orders, $documents, $response, $credentials);

            DB::commit();

            return response()->json([
                'success'   => true,
                'documents' => $stored
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function submitCreditNote(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'items' => 'required|array|min:1',
            'items.*.order_product_id' => 'required|exists:order_products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'reason' => 'nullable|string'
        ]);

        DB::beginTransaction();

        try {

            $order = Order::with([
                'doctor.clinic.address',
                'subscriber.address',
                'products'
            ])->where('id',$request->order_id)
            ->firstOrFail();

            $subscriberId = auth('admin')->user()->subscriber_id;

            if ($order->subscriber_id !== $subscriberId) {
                throw new \Exception('Order does not belong to this subscriber.');
            }

            $subscriber = $order->subscriber;

            $credentials = $subscriber->zatcaCredential()
                ->where('environment', config('services.zatca.environment'))
                ->first();

            if (!$credentials) {
                throw new \Exception('ZATCA credentials not found.');
            }

            $creditNote = $this->creditNoteService->createCreditNote(
                $order,
                $request->items,
                $request->reason
            );

            $response = $this->creditNoteService->submitCreditNote(
                $creditNote,
                $credentials
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'credit_note_id' => $creditNote->id,
                'response' => $response
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
