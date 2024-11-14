<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clinic extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'has_special_price',
        'tax_number',
    ];
    public function doctors(){
        return $this->hasMany(Doctor::class);
    }
    public function products()
    {
        return $this->hasMany(ClinicProduct::class);
    }
}
