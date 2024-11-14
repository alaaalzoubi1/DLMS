<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClinicSubscriber extends Model
{
    use HasFactory;
    protected $fillable = [
        'clinic_id',
        'subscriber_id'
    ];
}
