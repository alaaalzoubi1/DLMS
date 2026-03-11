<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
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
        'country_code',
        'commercial_registration',
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
    public function specialization_subscriber(): HasMany
    {
        return $this->hasMany(Specialization_Subscriber::class);
    }
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function types(): HasMany
    {
        return $this->hasMany(Type::class);
    }
    public function toothcolors(): HasMany
    {
        return $this->hasMany(ToothColor::class);
    }

    public function zatcaCredential(): HasOne
    {
        return $this->hasOne(SubscriberZatcaCredential::class);
    }
    public function address(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable');
    }
}
