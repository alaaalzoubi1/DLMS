<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriberZatcaCredential extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'subscriber_id',
        'private_key',
        'csr',
        'binary_security_token',
        'secret',
        'environment',
        'onboarded_at',
        'certificate_expiry_date',
        'last_invoice_hash',
        'last_icv'
    ];

    protected $casts = [
        'onboarded_at' => 'datetime',
        'certificate_expiry_date' => 'date',
    ];

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(Subscriber::class);
    }

    public static function isValidForSubscriber(
        int $subscriberId,
        string $environment
    ): bool {
        $credential = self::query()
            ->where('subscriber_id', $subscriberId)
            ->where('environment', $environment)
            ->first();

        if (!$credential) {
            return false;
        }

        if (!$credential->onboarded_at) {
            return false;
        }

        if (!$credential->certificate_expiry_date) {
            return false;
        }

        if ($credential->certificate_expiry_date->isPast()) {
            return false;
        }

        if (!$credential->binary_security_token || !$credential->secret) {
            return false;
        }

        return true;
    }

}

