<?php

namespace App\Models;

use App\Models\Scopes\CountryScope;
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
        'country_code'
    ];
    protected static function booted()
    {
        static::addGlobalScope(new CountryScope);
    }

}
