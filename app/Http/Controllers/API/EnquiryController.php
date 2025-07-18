<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductEnquiry;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class EnquiryController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'mobile_no' => 'required|string|max:15',
                'email_id' => 'nullable|email|max:255',
                'description' => 'required|string',
                'product_id' => 'required|exists:products,id',
                'user_id' => 'required|exists:users,id',
            ]);

            $enquiry = ProductEnquiry::create($request->all());

            return response()->json(['message' => 'Enquiry submitted successfully', 'data' => $enquiry], 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }
}

