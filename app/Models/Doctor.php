<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
         'last_name'
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
}
