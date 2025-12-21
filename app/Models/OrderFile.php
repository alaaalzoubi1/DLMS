<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'file_path',
        'original_name',
        'extension',
        'size',
        'mime_type',
        'status',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
