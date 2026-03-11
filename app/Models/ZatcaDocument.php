<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ZatcaDocument extends Model
{
    protected $fillable = [
        'subscriber_id',
        'order_id',
        'uuid',
        'icv',
        'previous_invoice_hash',
        'invoice_hash',
        'qr_code',
        'cleared_invoice',
        'info_messages',
        'error_messages',
        'warning_messages',
        'zatca_invoice_number',
        'clearance_status',
        'zatca_http_status',
        'zatca_response_json',
        'request_payload',
        'sent_at',
        'total_amount',
        'total_net_amount',
        'total_vat_amount',
        'invoice_type'
    ];

    protected $casts = [
        'info_messages' => 'array',
        'error_messages'   => 'array',
        'warning_messages' => 'array',
        'request_payload' =>'array',
        'sent_at'          => 'datetime',
        'icv'              => 'integer',
    ];
    protected $hidden = [
        'invoice_hash',
        'previous_invoice_hash',
        'icv',
        'uuid',
        'request_payload'
    ];
    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(Subscriber::class);
    }
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
