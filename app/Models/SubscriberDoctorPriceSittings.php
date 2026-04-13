<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriberDoctorPriceSittings extends Model
{
    protected $fillable =  [
        'doctor_account_id',
        'subscriber_id',
        'hide_prices'
    ];
}
