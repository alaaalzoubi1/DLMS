<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'subscriber_id',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function productsWithClinicPrice($clinicId)
    {
        return $this->hasMany(Product::class)
            ->withClinicPrice($clinicId);
    }
}
