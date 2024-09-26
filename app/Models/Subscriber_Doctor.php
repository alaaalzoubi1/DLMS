<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscriber_Doctor extends Model
{
    use HasFactory;
    protected $fillable = [
        'subscriber_id',
        'doctor_id'
    ];
    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
