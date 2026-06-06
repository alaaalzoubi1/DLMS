<?php

namespace App\Models;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Doctor_Account extends Authenticatable implements ShouldQueue , JWTSubject
{
    use HasFactory,HasApiTokens,SoftDeletes,Notifiable;
    protected array $guard = ["api"];
    protected $fillable = [
        'email',
        'password',
        'doctor_id',
        'FCM_token',
        'reset_code',
        'reset_expires_at'
    ];
    protected $hidden = [
        'password',
        'remember_token',
        'reset_code',
        'reset_expires_at'
    ];
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'reset_expires_at' => 'datetime'
    ];


    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
