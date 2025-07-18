<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DocumentUpload;
use App\Models\DocumentName;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DocumentUploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'document_name' => 'required|string',
            'order_id' => 'required|integer',
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf,zip,doc,docx,xls,xlsx|max:10240',
            'other_document' => 'nullable|string'
        ]);

        $file = $request->file('file');
        $fileName = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();

        $file->storeAs('documents', $fileName, 'public');

        DocumentUpload::create([
            'document_name' => $request->document_name,
            'document_type' => $extension,
            'file_name' => $fileName,
            'order_id' => $request->order_id,
            'other_document' => $request->document_name === 'Other' ? $request->other_document : null
        ]);

        return redirect()->back()->with('success', 'Document uploaded successfully.');
    }
}
