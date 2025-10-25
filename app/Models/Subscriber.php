<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscriber extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'company_name',
        'company_code',
        'trial_start_at',
        'trial_end_at',
        'tax_number',

        ];
    protected $casts = [
        'trial_end_at' => 'datetime',
    ];
    public function clinics()
    {
        return $this->belongsToMany(Clinic::class, 'clinic_subscribers');
    }

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
        return $this->belongsToMany(
            Specialization::class,
            'specialization__subscribers',
            'subscriber_id',
            'specializations_id'
        )->withTimestamps();
    }
    public function specialization_subscriber()
    {
        return $this->hasMany(Specialization_Subscriber::class);
    }
    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function categories()
    {
        return $this->hasMany(Category::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function types()
    {
        return $this->hasMany(Type::class);
    }
    public function toothcolors()
    {
        return $this->hasMany(ToothColor::class);
    }
}
