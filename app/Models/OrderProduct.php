<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'order_id',
        'tooth_color_id',
        'tooth_number',
    ];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
