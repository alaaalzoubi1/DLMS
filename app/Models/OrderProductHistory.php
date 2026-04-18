<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderProductHistory extends Model
{
    protected $fillable = [
        'order_product_id',
        'user_id',
        'subscriber_id',
        'specialization_name',
    ];

    public function orderProduct(): BelongsTo
    {
        return $this->belongsTo(OrderProduct::class);
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
