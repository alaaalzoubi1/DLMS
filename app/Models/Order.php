<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'doctor_id', 'subscriber_id', 'type_id', 'status', 'invoiced',
        'paid', 'cost', 'patient_name', 'receive', 'delivery', 'patient_id', 'specialization'
    ];

    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class);
    }
    public function products()
    {
        return $this->hasMany(OrderProduct::class)->with('product');
    }




}
