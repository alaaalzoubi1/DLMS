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
    public function specializationSubscriber()
    {
        return $this->belongsTo(Specialization_Subscriber::class, 'subscriber_specializations_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
