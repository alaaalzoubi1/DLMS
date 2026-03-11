<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderProduct extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'order_id',
        'tooth_color_id',
        'tooth_numbers',
        'specialization_users_id',
        'note',
        'product_name',
        'unit_price'
    ];
    protected $casts = [
        'tooth_numbers' => 'array',
        'unit_price' => 'decimal:2'
    ];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function specializationUser()
    {
        return $this->belongsTo(Specialization_User::class, 'specialization_users_id');
    }

    public function specialization()
    {
        return $this->hasOneThrough(
            Specialization::class, // Final model we want to reach
            Specialization_User::class, // Intermediate model
            'id', // Foreign key on SpecializationSubscribers (matches SpecializationUser's `subscriber_specializations_id`)
            'id', // Foreign key on Specializations
            'specialization_users_id', // Local key on Orders
            'specializations_id' // Local key on SpecializationSubscribers
        );
    }
    public function toothColor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ToothColor::class);
    }
    public function order(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
    public function scopeWorkingOn($query)
    {
        return $query->where('status','working');
    }
    public function creditNoteItems(): HasMany
    {
        return $this->hasMany(CreditNoteItem::class);
    }
}
