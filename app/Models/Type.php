<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    use HasFactory;
    protected $fillable = ['subscriber_id', 'type', 'invoiced'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
