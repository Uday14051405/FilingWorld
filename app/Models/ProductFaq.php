<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductFaq extends Model
{
    use HasFactory;
    protected $table = 'product_faqs';
    protected $fillable = [
        'title', 'description', 'service_id', 'status'
    ];
    protected $casts = [
        'service_id'    => 'integer',
        'status'    => 'integer',
    ];
    public function service(){
        return $this->belongsTo(Product::class,'service_id', 'id');
    }
}
