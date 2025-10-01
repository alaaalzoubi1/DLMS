<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'category_id',
        'price',
        'tooth_color_id',
        'tooth_number',
        'specialization_users_id'
    ];
    protected $appends = ['final_price'];

    protected $hidden = ['price'];

    public function getFinalPriceAttribute()
    {
        $clinicId = null;
        if (auth('api')->check() && auth('api')->user()->doctor) {
            $clinicId = auth('api')->user()->doctor->clinic_id;
        }



        if (!$clinicId) {
            return $this->price;
        }

        $specialPrice = DB::table('clinic_products')
            ->where('clinic_id', $clinicId)
            ->where('product_id', $this->id)
            ->value('price');

        return $specialPrice ?? $this->price;
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function clinics()
    {
        return $this->hasMany(ClinicProduct::class);
    }
    public function specializationUser()
    {
        return $this->belongsTo(Specialization_User::class);
    }

    public function scopeWithClinicPrice(Builder $query, $clinicId)
    {
        return $query->leftJoin('clinic_products', function ($join) use ($clinicId) {
            $join->on('products.id', '=', 'clinic_products.product_id')
                ->where('clinic_products.clinic_id', '=', $clinicId);
        })
            ->select(
                'products.*',
                DB::raw('COALESCE(clinic_products.price, products.price) as final_price')
            );
    }

}
