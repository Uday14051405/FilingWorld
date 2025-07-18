<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Translations extends Model
{

    protected $fillable = ['locale', 'key', 'value', 'translatable_id', 'translatable_type'];

    public function translatable()
    {
        return $this->morphTo();
    }
}

