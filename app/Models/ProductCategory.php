<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\TranslationTrait;

class ProductCategory extends BaseModel implements HasMedia
{
    use HasFactory,HasRoles,InteractsWithMedia,SoftDeletes;
    use TranslationTrait;
    protected $table = 'product_categories';
    protected $fillable = [
        'name', 'description', 'is_featured', 'status' , 'color', 'menu_category', 'submenu_category'
    ];
    protected $casts = [
        'status'    => 'integer',
        'is_featured'  => 'integer',
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


    public function services(){
        return $this->hasMany(Product::class, 'category_id','id');
    }
    public function scopeList($query)
    {
        return $query->orderByRaw('deleted_at IS NULL DESC, deleted_at DESC')->orderBy('updated_at', 'desc');
    }
    public function menuCategory()
    {
        return $this->belongsTo(ProductMenuCategory::class, 'menu_category'); 
    }

    public function submenuCategory()
    {
        return $this->belongsTo(ProductSubMenu::class, 'submenu_category'); 
    }
    
    public function subCategories()
    {
        return $this->hasMany(ProductSubCategory::class, 'category_id', 'id');
    }
}
