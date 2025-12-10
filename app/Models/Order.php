<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'doctor_id', 'subscriber_id', 'type_id', 'status', 'invoiced',
        'paid', 'cost', 'patient_name', 'receive', 'delivery', 'patient_id'
    ];

    public function type(): BelongsTo
    {
        return $this->belongsTo(Type::class)->withTrashed();
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(Subscriber::class);
    }
    public function products(): HasMany
    {
        return $this->hasMany(OrderProduct::class)->with(['toothColor:id,color']);
    }
    public function discount(): HasOne
    {
        return $this ->hasOne(OrderDiscount::class);
    }




}
