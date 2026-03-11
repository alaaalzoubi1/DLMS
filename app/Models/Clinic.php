<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Clinic extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'has_special_price', 'tax_number','clinic_code','commercial_registration'];

    public function subscribers(): BelongsToMany
    {
        return $this->belongsToMany(Subscriber::class, 'clinic_subscribers');
    }

    public function doctors(): HasMany
    {
        return $this->hasMany(Doctor::class);
    }
    public function products(): HasMany
    {
        return $this->hasMany(ClinicProduct::class);
    }

    public function address(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable');
    }

}
