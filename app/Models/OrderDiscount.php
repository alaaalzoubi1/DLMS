<?php

namespace App\Models;

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static findOrFail($id)
 */
class OrderDiscount extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id', 'subscriber_id', 'type', 'amount'
    ];



    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
