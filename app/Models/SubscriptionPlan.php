<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriptionPlan extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'duration_days',
        'price',
        'description',
    ];


}
