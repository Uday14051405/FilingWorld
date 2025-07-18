<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentName extends Model
{
    use HasFactory;
    protected $table = 'document_names';
    protected $fillable = [
        'document_name',
        'status',
    ];
}
