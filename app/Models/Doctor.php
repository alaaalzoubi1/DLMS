<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class Doctor extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'first_name',
         'last_name',
        'clinic_id',
    ];
    public function subscribers()
    {
        return $this->belongsToMany(Subscriber::class)->withTimestamps();
    }
    public function doctorsSubscriptions()
    {
        return $this->hasMany(Subscriber_Doctor::class);
    }
    public function account()
    {
        return $this->hasOne(Doctor_Account::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }
    protected static function booted()
    {
        static::created(function ($order) {
            static::clearDashboardCache($order->subscriber_id);
        });

        static::updated(function ($order) {
            static::clearDashboardCache($order->subscriber_id);
        });

        static::deleted(function ($order) {
            static::clearDashboardCache($order->subscriber_id);
        });
    }

    protected static function clearDashboardCache($subscriberId)
    {
        $periods = ['today', 'week', 'month', 'all'];
        foreach ($periods as $period) {
            $cacheKey = "dashboard_stats_{$subscriberId}_{$period}";
            Cache::forget($cacheKey);
        }
    }
}
