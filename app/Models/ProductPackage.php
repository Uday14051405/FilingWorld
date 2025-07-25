<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Traits\TranslationTrait;

class ProductPackage extends Model implements  HasMedia
{
    use InteractsWithMedia,HasFactory;
    use TranslationTrait;
    protected $table = 'product_packages';
    protected $fillable = [
        'name', 'description', 'provider_id', 'status' , 'price','start_at','end_at','is_featured','category_id','subcategory_id','package_type'
    ];
    protected $casts = [
        'provider_id'    => 'integer',
        'status'    => 'integer',
        'price'  => 'double',
        'is_featured' => 'integer',
        'category_id' => 'integer',
        'subcategory_id' => 'integer',

    ];
    public function translations()
    {
        return $this->morphMany(Translations::class, 'translatable');
    }

   public function translate($attribute, $locale = null)
    {
        
        $locale = $locale ?? app()->getLocale() ?? 'en';
        if($locale !== 'en'){
            $translation = $this->translations()
            ->where('attribute', $attribute)
            ->where('locale', $locale)
            ->value('value');

        return $translation !== null ?  $translation : '';
        }
        return $this->$attribute;
    }
    public function packageServices(){
        return $this->hasMany(PackageProductMapping::class, 'service_package_id','id');
    }
    public function category(){
        return $this->belongsTo('App\Models\ProductCategory','category_id','id');
    } 
    public function subcategory(){
        return $this->belongsTo('App\Models\ProductSubCategory','subcategory_id','id');
    }
    public function providers(){
        return $this->belongsTo('App\Models\User','provider_id','id');
    }
    public function bookingPackageMappings(){
        return $this->hasMany(BookingProductPackageMapping::class, 'service_package_id','id');
    }
   
    public function scopeMyPackage($query)
    {
        if(auth()->user()->hasRole('admin')) {
            return $query;
        }

        if(auth()->user()->hasRole('provider')) {
            return $query->where('provider_id', \Auth::id());
        }

        return $query;
    }
    public function getTotalPrice()
    {
        return $this->packageServices->sum(function ($packageService) {
            return $packageService->service->price ?? 0; // Adjust according to your relationship
        });
    }
}
