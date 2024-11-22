<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clinic extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'has_special_price', 'tax_number'];

    public function subscribers()
    {
        return $this->belongsToMany(Subscriber::class, 'clinic_subscribers', 'clinic_id', 'subscriber_id');
    }
    public function doctors(){
        return $this->hasMany(Doctor::class);
    }
    public function products()
    {
        return $this->hasMany(ClinicProduct::class);
    }
}
