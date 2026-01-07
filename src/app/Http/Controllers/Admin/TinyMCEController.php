<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TinyMCEController extends Controller
{
    /**
     * Handle image upload from TinyMCE editor
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        try {
            if ($request->hasFile('file')) {
                $file = $request->file('file');

                // Generate unique filename
                $filename = Str::random(20) . '_' . time() . '.' . $file->getClientOriginalExtension();

                // Store in public disk under tinymce folder
                $path = $file->storeAs('uploads/tinymce', $filename, 'public');

                // Get the full URL
                $url = Storage::disk('public')->url($path);

                return response()->json([
                    'location' => $url,
                    'path' => $path
                ], 200);
            }

            return response()->json([
                'error' => 'No file uploaded'
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
