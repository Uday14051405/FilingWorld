<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentUpload extends Model
{
    protected $table = 'document_uploads';
    protected $fillable = [
        'document_name',
        'document_type',
        'file_name',
        'order_id',
        'other_document',
    ];
}
