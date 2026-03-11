<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Address extends Model
{
    use HasFactory;
    protected $fillable = [
        'street',
        'building_number',
        'additional_number',
        'district',
        'city',
        'postal_code',
        'locationAddress'
    ];

    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }
}
