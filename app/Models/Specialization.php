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
        return $this->belongsToMany(Subscriber::class)->withTimestamps();
    }
    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
