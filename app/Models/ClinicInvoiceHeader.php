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
    protected $hidden = ['logo'];

    protected $appends = ['logo_url'];

    public function getLogoUrlAttribute(): ?string
    {
        if (!$this->logo) {
            return null;
        }

        return env('APP_URL') . '/storage/' . $this->logo;
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }
}
