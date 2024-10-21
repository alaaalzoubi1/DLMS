<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specialization_User extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'subscriber_specializations_id',
    ];
//    public function users(){
//        return $this->hasMany(User::class);
//    }

}
