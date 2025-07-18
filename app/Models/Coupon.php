<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'coupons';
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'code', 'discount_type', 'discount', 'expire_date', 'status','type'
    ];
    
    protected $casts = [
        'discount'  => 'double',
        'status'    => 'integer',
    ];

    protected static function boot(){
        parent::boot();
    
        static::deleted(function ($row) {
            $row->serviceAdded()->delete();
            $row->productAdded()->delete(); // Add this line
    
            if ($row->forceDeleting === true) {
                $row->serviceAdded()->forceDelete();
                $row->productAdded()->forceDelete(); // Add this line
            }
        });
    
        static::restoring(function($row) {
            $row->serviceAdded()->withTrashed()->restore();
            $row->productAdded()->withTrashed()->restore(); // Add this line
        });
    }
    

    public function serviceAdded(){
        return $this->hasMany(CouponServiceMapping::class,'coupon_id','id');
    }

    public function productAdded(){
        return $this->hasMany(CouponProductMapping::class,'coupon_id','id');
    }

    public function getExpireDateAttribute($value) {
        if($value!=null)
            return $this->attributes['expire_date'] = Carbon::parse($value)->format('Y-m-d H:i');
    }
    public function scopeList($query)
    {
        return $query->orderByRaw('deleted_at IS NULL DESC, deleted_at DESC')->orderBy('updated_at', 'desc');
    }
    public function serviceCoupons()
    {
        return $this->belongsToMany(Service::class, 'coupon_service_mappings', 'coupon_id', 'service_id');
    }
    public function productCoupons()
    {
        return $this->belongsToMany(Product::class, 'coupon_product_mappings', 'coupon_id', 'service_id');
    }

}
