<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable  implements ShouldQueue , JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable,HasRoles,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guard = 'admin';
    protected $guard_name = 'admin';
    protected $fillable = [
        'email',
        'password',
        'last_name',
        'first_name',
        'subscriber_id',
        'FCM_token'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value associative array representing the authentication identity.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
    public function specializations()
    {
        return $this->belongsToMany(Specialization::class, 'specialization__users', 'user_id', 'subscriber_specializations_id')
            ->join('specialization__subscribers', 'specialization__users.subscriber_specializations_id', '=', 'specialization__subscribers.id')
            ->join('specializations as sub_specializations', 'specialization__subscribers.specializations_id', '=', 'sub_specializations.id')
            ->select('sub_specializations.name');
    }


    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function subscribers()
    {
        return $this->belongsTo(Subscriber::class,'subscriber_id');
    }
    public function specializationSubscribers()
    {
        return $this->belongsToMany(Specialization_Subscriber::class, 'specialization__users');
    }
}
