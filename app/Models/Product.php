<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'subscriber_id',
    ];

    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class);
    }
}
