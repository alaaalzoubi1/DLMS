<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specialization_Subscriber extends Model
{
    protected $fillable = [
        'subscriber_id',
        'specializations_id',
    ];
    use HasFactory;
    public function users(){
        return $this->belongsToMany(User::class,'specialization__users','subscriber_specializations_id','user_id')->withTimestamps();
    }
    public function specialization()
    {
        return $this->belongsTo(Specialization::class, 'specializations_id');
    }
}
