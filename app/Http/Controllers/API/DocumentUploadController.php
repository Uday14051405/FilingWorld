<?php

namespace App\Http\Controllers\API;

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

        $document = DocumentUpload::create([
            'document_name' => $request->document_name,
            'document_type' => $extension,
            'file_name' => $fileName,
            'order_id' => $request->order_id,
            'other_document' => $request->document_name === 'Other' ? $request->other_document : null
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Document uploaded successfully',
            'data' => $document
        ]);
    }

    public function show($order_id)
    {
        $documents = DocumentUpload::where('order_id', $order_id)->get(['document_name', 'file_name', 'other_document']);

        if ($documents->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No documents found for this order'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $documents
        ]);
    }

    public function getActiveDocumentNames()
    {
        $documentNames = DocumentName::where('status', 1)
            ->pluck('document_name');

        return response()->json([
            'status' => true,
            'data' => $documentNames
        ]);
    }
}
