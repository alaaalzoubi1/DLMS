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
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function specialization()
    {
        return $this->belongsTo(Specialization::class, 'sub_spec_id');
    }
}
