<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicProduct extends Model
{
    use HasFactory;
    protected $fillable = [
        'clinic_id',
        'product_id',
        'price',
    ];
    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
