<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabInvoiceHeader extends Model
{
    use HasFactory;
    protected $fillable = [
        'subscriber_id',
        'lab_name_ar',
        'lab_name_en',
        'address',
        'logo'
    ];

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(Subscriber::class);
    }
}
