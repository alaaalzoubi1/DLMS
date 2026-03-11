<?php

namespace App\Zatca\Services;

use App\Models\CreditNote;
use App\Models\Order;
use App\Models\SubscriberZatcaCredential;
use App\Models\ZatcaDocument;
use App\ValueObjects\Money;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ZatcaInvoiceService
{
    protected ZatcaApiClient $client;

    public function __construct(ZatcaApiClient $client)
    {
        $this->client = $client;
    }

    /**
     * بناء مصفوفة documents من الطلبات
     * @throws Exception
     */
    public function buildBulkPayload(Collection $orders, SubscriberZatcaCredential $credentials): array
    {
        $documents = [];
        $nextIcv = $credentials->last_icv + 1;
        $lastHash = $credentials->last_invoice_hash;
        $lastDocs = ZatcaDocument::whereIn('order_id', $orders->pluck('id'))
            ->latest()
            ->get()
            ->keyBy('order_id');

        foreach ($orders as $order) {

            $lastZatcaDoc = $lastDocs[$order->id] ?? null;

            // الحالة 1: لا يوجد مستند سابق
            if (!$lastZatcaDoc) {

                $documents[] = $this->buildInvoiceDocument(
                    $order,
                    $nextIcv,
                    $lastHash
                );

                $nextIcv++;
                continue;
            }

            $statusCode = $lastZatcaDoc->zatca_http_status;

            // الحالة 2: 404 أو 429 أو 500+
            if (
                in_array($statusCode, [404, 429]) ||
                $statusCode >= 500
            ) {

                // إعادة الإرسال بنفس القيم السابقة
                $documents[] = $lastZatcaDoc->request_payload;

                continue;
            }

            // الحالة 3: 400 → إعادة بناء بسلسلة جديدة
            if ($statusCode == 400) {

                $documents[] = $this->buildInvoiceDocument(
                    $order,
                    $nextIcv,
                    $credentials->last_invoice_hash
                );

                $nextIcv++;
                continue;
            }

            // أي حالة أخرى → تعامل كفاتورة جديدة
            $documents[] = $this->buildInvoiceDocument(
                $order,
                $nextIcv,
                $credentials->last_invoice_hash
            );

            $nextIcv++;
        }

        return $documents;
    }
    /**
     * بناء فاتورة عادية
     * @throws Exception
     */
    protected function buildInvoiceDocument(Order $order, int $icv, string $previousHash): array
    {
        if (!$order->delivery || !$order->receive) {
            throw new \Exception("Receive or delivery date is missing for order {$order->id}");
        }
        $subscriber = $order->subscriber;
        $clinic     = $order->doctor->clinic;

        $this->validateAddresses($order, $subscriber, $clinic);

        $invoiceLines = $this->buildInvoiceLines($order);
        $subtotal = collect($invoiceLines)->reduce(function ($carry, $line) {
            $price = Money::fromMajor($line['price']);
            $lineTotal = $price->multiply($line['quantity']);
            return $carry->add($lineTotal);
        }, Money::fromMinor(0));
        $allowances   = $this->buildAllowances($order, $subtotal);

        $invoiceNumber = 'INV-' . str_pad($order->id, 8, '0', STR_PAD_LEFT);

        return [
            'invoiceType'      => 'TAX_INVOICE',
            'generalInvoiceInfo' => [
                'number'              => $invoiceNumber,
                'uuid'                => (string) Str::uuid(),
                'icv'                 => $icv,
                'issueDateTime'       => $order->receive,
                'actualDeliveryDate'  => $order->delivery,
                'previousInvoiceHash' => $previousHash,
                'currency'            => 'SAR',
                'paymentMeans'        => ['30', '10'],
                'purchaseOrder'       => null,
                'contractNumber'      => null,
            ],
            'seller' => $this->buildParty($subscriber, 'seller'),
            'buyer'  => $this->buildParty($clinic, 'buyer'),
            'allowances' => $allowances,
            'legalMonetaryTotal' => [
                'totalVatAmount' => 0,
            ],
            'invoiceLines' => $invoiceLines,
        ];
    }

    /**
     * بناء بنود الفاتورة (بدون إجماليات)
     */
    protected function buildInvoiceLines(Order $order): array
    {
        $lines = [];

        foreach ($order->products as $product) {
            $toothNumbers = $product->tooth_numbers?? [];
            $quantity = count($toothNumbers) ?: 1;

            $lines[] = [
                'quantity' => $quantity,
                'price'    => (float) $product->unit_price,
                'itemName' => $product->product_name,
                'vat'      => [
                    'categoryCode'              => 'Z',
                    'percent'                   => 0,
                    'taxExemptionReasonCode'    => 'VATEX-SA-35',
                    'taxExemptionReason'        => 'Medicines and medical equipment',
                ],
            ];
        }

        return $lines;
    }

    /**
     * بناء الخصومات (على مستوى الفاتورة)
     */
    protected function buildAllowances(Order $order, Money $subtotal): array
    {
        if (!$discount = $order->discount) {
            return [];
        }

        $allowance = [
            'reason'          => 'Discount',
            'reasonCode'      => '95',
            'vat'             => [
                'categoryCode' => 'Z',
                'percent'      => 0,
            ],
            'chargeIndicator' => false,
        ];

        if ($discount->type === 'percentage') {

            $basisPoints = (int) round($discount->amount * 100);

            $discountMoney = $subtotal->percentage($basisPoints);

            $allowance['percent']    = number_format($discount->amount, 2, '.', '');
            $allowance['baseAmount'] = $subtotal->toMajor();

        } else {
            $discountMoney = Money::fromMajor($discount->amount);
        }
        $allowance['amount']     = $discountMoney->toMajor();

        return [$allowance];
    }

    /**
     * بناء بيانات الطرف (بائع أو مشتري)
     */
    protected function buildParty($party, string $type): array
    {
        $isBuyer = ($type === 'buyer');
        $address = $party->address;

        $idType = 'CRN';
        $idValue = $party->commercial_registration;

        return [
            'name' => $isBuyer
                ? $party->name
                : $party->company_name,
            'address' => [
                'street'           => $address->street ?? '',
                'buildingNumber'   => $address->building_number ?? '0000',
                'additionalNumber' => $address->additional_number ?? '0000',
                'district'         => $address->district,
                'city'             => $address->city,
                'postalCode'       => $address->postal_code ?? '00000',
                'countryCode'      => $isBuyer ? 'SA' : ($party->country_code ?? 'SA'),
            ],
            'vatNumber' => $party->tax_number,
            'id'        => [
                'idType' => $idType,
                'value'  => $idValue,
            ],
        ];
    }

    /**
     * التحقق من وجود العناوين
     * @throws Exception
     */
    protected function validateAddresses(Order $order, $subscriber, $clinic): void
    {
        if (!$subscriber->address) {
            throw new Exception("Subscriber address not found for order {$order->id}");
        }
        if (!$clinic->address) {
            throw new Exception("Clinic address not found for order {$order->id}");
        }
    }

    /**
     * إرسال الفواتير
     * @throws Exception
     */
    public function sendBulkInvoice(array $documents, SubscriberZatcaCredential $credentials): array
    {
        $payload = [
            'documents'           => $documents,
            'privateKey'          => $credentials->private_key,
            'binarySecurityToken' => $credentials->binary_security_token,
            'secret'              => $credentials->secret,
        ];

        return $this->client->post('/submit/bulk', $payload);
    }

    /**
     * تخزين نتيجة الإرسال وتحديث last_icv / last_invoice_hash
     */
    public function storeBulkResponse(
        Collection $orders,
        array $documents,
        array $response,
        SubscriberZatcaCredential $credentials
    ): Collection
    {

        $rows = [];
        $stored = [];

        $maxIcv = $credentials->last_icv;
        $lastSuccessfulHash = $credentials->last_invoice_hash;

        foreach ($response as $index => $invoiceResponse) {

            $documentData = $documents[$index] ?? null;
            if (!$documentData) {
                continue;
            }

            $invoiceNumber = $documentData['generalInvoiceInfo']['number'];
            $orderId = (int) substr($invoiceNumber, 4);
            $order = $orders->firstWhere('id', $orderId);

            if (!$order) {
                continue;
            }

            $infoMessages = $invoiceResponse['infoMessages'] ?? [];
            $errorMessages = $invoiceResponse['errorMessages'] ?? [];
            $warningMessages = $invoiceResponse['warningMessages'] ?? [];

            $status = $invoiceResponse['status'] ?? 'UNKNOWN';
            $invoiceHash = $invoiceResponse['invoiceHash'] ?? null;
            $qrCode = $invoiceResponse['qrCode'] ?? null;
            $clearedInvoice = $invoiceResponse['invoice'] ?? null;
            $zatcaHttpStatus = $invoiceResponse['zatcaHttpStatus'] ?? null;

            $icv = $invoiceResponse['icv'] ?? $documentData['generalInvoiceInfo']['icv'];
            $previousHash = $invoiceResponse['previousInvoiceHash']
                ?? $documentData['generalInvoiceInfo']['previousInvoiceHash'];

            $totalAmount = $invoiceResponse['totalAmount'] ?? null;
            $totalVatAmount = $invoiceResponse['totalAmountWithVat'] ?? null;
            $totalNetAmount = $invoiceResponse['sumOfLineNetAmount'] ?? null;

            $clearedInvoicePath = null;

            if ($clearedInvoice) {

                $xml = base64_decode($clearedInvoice);

                $fileName = $documentData['generalInvoiceInfo']['uuid'] . '.xml';

                $path = 'invoices/' . Carbon::now('Asia/Riyadh')->format('Y/m/') . $fileName;

                Storage::disk('private')->put($path, $xml);

                $clearedInvoicePath = $path;
            }

            $rows[] = [
                'invoice_type' => 'TAX_INVOICE',
                'subscriber_id' => $order->subscriber_id,
                'order_id' => $order->id,
                'zatca_invoice_number' => $invoiceNumber,
                'uuid' => $documentData['generalInvoiceInfo']['uuid'],
                'icv' => $icv,
                'previous_invoice_hash' => $previousHash,
                'invoice_hash' => $invoiceHash,
                'qr_code' => $qrCode,
                'cleared_invoice' => $clearedInvoicePath,
                'info_messages' => json_encode($infoMessages),
                'error_messages' => json_encode($errorMessages),
                'warning_messages' => json_encode($warningMessages),
                'clearance_status' => $status,
                'zatca_http_status' => $zatcaHttpStatus,
                'request_payload' => json_encode($documentData),
                'total_amount' => $totalAmount,
                'total_net_amount' => $totalNetAmount,
                'total_vat_amount' => $totalVatAmount,
                'sent_at' => Carbon::now('Asia/Riyadh'),
                'created_at' => Carbon::now('Asia/Riyadh'),
                'updated_at' => Carbon::now('Asia/Riyadh'),
            ];

            // تحديث ICV
            if ($icv > $maxIcv) {
                $maxIcv = $icv;
            }

            // تحديث hash حتى لو فشلت الفاتورة
            if ($invoiceHash) {
                $lastSuccessfulHash = $invoiceHash;
            }
        }

        if (!empty($rows)) {
            ZatcaDocument::insert($rows);
            $stored = $rows;
        }

        $credentials->last_icv = $maxIcv;
        $credentials->last_invoice_hash = $lastSuccessfulHash;
        $credentials->save();
        return collect($rows)->map(function ($doc) {

            $doc['info_messages'] = json_decode($doc['info_messages'], true);
            $doc['error_messages'] = json_decode($doc['error_messages'], true);
            $doc['warning_messages'] = json_decode($doc['warning_messages'], true);
            $doc['request_payload'] = json_decode($doc['request_payload'], true);

            return $doc;
        });
    }
    public function sendCreditNote(array $document, SubscriberZatcaCredential $credentials): array
    {
        $payload = [
            'document' => $document,
            'privateKey' => $credentials->private_key,
            'binarySecurityToken' => $credentials->binary_security_token,
            'secret' => $credentials->secret,
        ];

        return $this->client->post('/submit', $payload);
    }

    /**
     * @throws Exception
     */
    public function buildCreditNoteDocument(
        Order $order,
        CreditNote $creditNote,
        ZatcaDocument $invoiceDocument,
        int $icv,
        string $previousHash
    ): array
    {
        if (!$order->delivery || !$order->receive) {
            throw new \Exception("Receive or delivery date is missing for order {$order->id}");
        }

        $subscriber = $order->subscriber;
        $clinic = $order->doctor->clinic;

        $this->validateAddresses($order, $subscriber, $clinic);

        $invoiceLines = $this->buildCreditNoteLines($creditNote);

        $invoiceNumber = 'CN-' . str_pad($creditNote->id, 8, '0', STR_PAD_LEFT);

        return [
            'invoiceType' => 'CREDIT_NOTE',

            'generalInvoiceInfo' => [
                'number' => $invoiceNumber,
                'uuid' => (string) Str::uuid(),
                'icv' => $icv,
                'issueDateTime' => $order->receive,
                'actualDeliveryDate' => $order->delivery,
                'previousInvoiceHash' => $previousHash,
                'currency' => 'SAR',
                'paymentMeans' => ['10','30'],
            ],

            'seller' => $this->buildParty($subscriber, 'seller'),
            'buyer' => $this->buildParty($clinic, 'buyer'),

            'invoiceLines' => $invoiceLines,

            'billingReference' => $invoiceDocument->zatca_invoice_number,
            'reasonsForIssuance' => $creditNote->reason ?? 'Return',
        ];
    }
    protected function buildCreditNoteLines(CreditNote $creditNote): array
    {
        $lines = [];

        foreach ($creditNote->items as $item) {

            $product = $item->orderProduct;

            $lines[] = [
                'quantity' => $item->quantity,
                'price'    => (float) $item->unit_price,
                'itemName' => $product->product_name,
                'vat'      => [
                    'categoryCode'           => 'Z',
                    'percent'                => 0,
                    'taxExemptionReasonCode' => 'VATEX-SA-35',
                    'taxExemptionReason'     => 'Medicines and medical equipment',
                ],
            ];
        }

        return $lines;
    }
    public function storeCreditNoteResponse(
        CreditNote $creditNote,
        array $document,
        array $response,
        SubscriberZatcaCredential $credentials
    ): ZatcaDocument {

        $status          = $response['status'] ?? 'UNKNOWN';
        $invoiceHash     = $response['invoiceHash'] ?? null;
        $qrCode          = $response['qrCode'] ?? null;
        $clearedInvoice  = $response['invoice'] ?? null;
        $zatcaHttpStatus = $response['zatcaHttpStatus'] ?? null;

        $infoMessages    = $response['infoMessages'] ?? [];
        $errorMessages   = $response['errorMessages'] ?? [];
        $warningMessages = $response['warningMessages'] ?? [];
        $totalAmount = $response['totalAmount'] ?? [];
        $sumOfLineNetAmount = $response['sumOfLineNetAmount'] ?? [];
        $totalAmountWithVat = $response['totalAmountWithVat'] ?? [];

        $icv          = $response['icv'] ?? $document['generalInvoiceInfo']['icv'];
        $previousHash = $response['previousInvoiceHash'] ?? $document['generalInvoiceInfo']['previousInvoiceHash'];

        $uuid = $document['generalInvoiceInfo']['uuid'];

        $clearedInvoicePath = null;

        if ($clearedInvoice) {

            $xml = base64_decode($clearedInvoice);

            $fileName = $uuid . '.xml';

            $path = 'credit-notes/' . Carbon::now('Asia/Riyadh')->format('Y/m/') . $fileName;
            Storage::disk('private')->put($path, $xml);

            $clearedInvoicePath = $path;
        }

        $zatcaDocument = ZatcaDocument::create([
            'invoice_type'          => 'CREDIT_NOTE',
            'subscriber_id'         => $creditNote->order->subscriber_id,
            'order_id'              => $creditNote->order_id,
            'credit_note_id'        => $creditNote->id,
            'zatca_invoice_number'  => $document['generalInvoiceInfo']['number'],
            'uuid'                  => $uuid,
            'icv'                   => $icv,
            'previous_invoice_hash' => $previousHash,
            'invoice_hash'          => $invoiceHash,
            'qr_code'               => $qrCode,
            'cleared_invoice'       => $clearedInvoicePath,
            'info_messages'         => $infoMessages,
            'error_messages'        => $errorMessages,
            'warning_messages'      => $warningMessages,
            'clearance_status'      => $status,
            'zatca_http_status'     => $zatcaHttpStatus,
            'request_payload'       => $document,
            'sent_at'               => Carbon::now('Asia/Riyadh'),
            'total_amount' => $totalAmount,
            'total_vat_amount' => $totalAmountWithVat,
            'total_net_amount' => $sumOfLineNetAmount
        ]);

        if ($icv > $credentials->last_icv) {
            $credentials->last_icv = $icv;
        }

        if ($invoiceHash) {
            $credentials->last_invoice_hash = $invoiceHash;
        }

        $credentials->save();

        return $zatcaDocument;
    }
}
