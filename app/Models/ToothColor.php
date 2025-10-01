<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ToothColor extends Model
{
    use HasFactory;
    protected $fillable = [
        'color',
        'subscriber_id',
    ];
    public function scopeNotDeleted($query)
    {
        return $query->where('is_deleted',false);
    }
}
