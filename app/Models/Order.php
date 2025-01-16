<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'doctor_id',
        'user_id',
        'status',
        'paid',
        'cost',
        'patient_name',
        'order_date',
        'patient_id',
        'sub_spec_id',
        'specialization_users_id',
        'receive',
        'specialization',
        'subscriber_id',
        'type'
    ];

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
    public function specializationUser()
    {
        return $this->belongsTo(Specialization_User::class, 'specialization_users_id');
    }

    public function specialization()
    {
        return $this->hasOneThrough(
            Specialization::class, // Final model we want to reach
            Specialization_User::class, // Intermediate model
            'id', // Foreign key on SpecializationSubscribers (matches SpecializationUser's `subscriber_specializations_id`)
            'id', // Foreign key on Specializations
            'specialization_users_id', // Local key on Orders
            'specializations_id' // Local key on SpecializationSubscribers
        );
    }



}
