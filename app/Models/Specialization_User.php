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
    public function specialization(): \Illuminate\Database\Eloquent\Relations\HasOneThrough
    {
        return $this->hasOneThrough(
            Specialization::class, // Final model we want to reach
            Specialization_Subscriber::class, // Intermediate model
            'id', // Foreign key on SpecializationSubscribers (matches SpecializationUser's `subscriber_specializations_id`)
            'id', // Foreign key on Specializations
            'subscriber_specializations_id', // Local key on Orders
            'specializations_id' // Local key on SpecializationSubscribers
        );
    }


}
