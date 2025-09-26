<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specialization extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
    ];
    public function subscribers()
    {
        return $this->belongsToMany(
            Subscriber::class,
            'specialization__subscribers',
            'specializations_id',
            'subscriber_id'
        )->withTimestamps();
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function specialization_subscribers(){
        return $this->hasMany(Specialization_Subscriber::class);
    }
}
