<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClinicInvoiceHeader extends Model
{
    protected $fillable = [
        'clinic_id',
        'clinic_name_ar',
        'clinic_name_en',
        'address',
        'logo'
    ];

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }
}
