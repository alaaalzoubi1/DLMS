<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    use HasFactory;
    protected $fillable = [
        'company_name',
        'company_code',
        'trial_start_at',
        'trial_end_at',
        ];
    public function doctors()
    {
        return $this->belongsToMany(Doctor::class)->withTimestamps();
    }
    public function doctorsSubscriptions()
    {
        return $this->hasMany(Subscriber_Doctor::class);
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function specializations()
    {
        return $this->belongsToMany(Specialization::class)->withTimestamps();
    }
}
