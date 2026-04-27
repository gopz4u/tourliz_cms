<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    /**
     * Upload a single image file
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function uploadImage(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
            ], [
                'image.required' => 'Please select an image file.',
                'image.image' => 'The file must be an image.',
                'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif, webp.',
                'image.max' => 'The image may not be greater than 5MB.',
            ]);

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $originalName = $file->getClientOriginalName();
                $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('public/images', $filename);
                
                // Return the path relative to storage/app/public
                $relativePath = 'images/' . $filename;
                
                // Use helper function to get URL with fallback
                $url = getImageUrl($relativePath);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Image uploaded successfully',
                    'data' => [
                        'url' => $url,
                        'path' => $relativePath,
                        'filename' => $filename,
                        'original_name' => $originalName,
                        'size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'uploaded_at' => now()->toISOString(),
                    ]
                ], 201);
            }

            return response()->json([
                'success' => false,
                'message' => 'No image file provided'
            ], 400);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload multiple image files
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function uploadMultipleImages(Request $request)
    {
        try {
            $request->validate([
                'images' => 'required|array',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max per image
            ], [
                'images.required' => 'Please select at least one image file.',
                'images.array' => 'Images must be an array.',
                'images.*.image' => 'All files must be images.',
                'images.*.mimes' => 'Images must be of type: jpeg, png, jpg, gif, webp.',
                'images.*.max' => 'Each image may not be greater than 5MB.',
            ]);

            if ($request->hasFile('images')) {
                $uploadedImages = [];
                
                foreach ($request->file('images') as $file) {
                    $originalName = $file->getClientOriginalName();
                    $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('public/images', $filename);
                    
                    // Return the path relative to storage/app/public
                    $relativePath = 'images/' . $filename;
                    
                    // Use helper function to get URL with fallback
                    $url = getImageUrl($relativePath);
                    
                    $uploadedImages[] = [
                        'url' => $url,
                        'path' => $relativePath,
                        'filename' => $filename,
                        'original_name' => $originalName,
                        'size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                    ];
                }
                
                return response()->json([
                    'success' => true,
                    'message' => 'Images uploaded successfully',
                    'data' => [
                        'images' => $uploadedImages,
                        'count' => count($uploadedImages),
                        'uploaded_at' => now()->toISOString(),
                    ]
                ], 201);
            }

            return response()->json([
                'success' => false,
                'message' => 'No image files provided'
            ], 400);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload a document/file (PDF, DOC, etc.)
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function uploadFile(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,txt,zip|max:10240', // 10MB max
            ], [
                'file.required' => 'Please select a file.',
                'file.file' => 'The uploaded file is not valid.',
                'file.mimes' => 'The file must be of type: pdf, doc, docx, xls, xlsx, txt, zip.',
                'file.max' => 'The file may not be greater than 10MB.',
            ]);

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '_' . Str::random(10) . '.' . $extension;
                $path = $file->storeAs('public/files', $filename);
                
                // Return the path relative to storage/app/public
                $relativePath = 'files/' . $filename;
                
                // Generate URL
                $url = url('storage/' . $relativePath);
                
                return response()->json([
                    'success' => true,
                    'message' => 'File uploaded successfully',
                    'data' => [
                        'url' => $url,
                        'path' => $relativePath,
                        'filename' => $filename,
                        'original_name' => $originalName,
                        'size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'extension' => $extension,
                        'uploaded_at' => now()->toISOString(),
                    ]
                ], 201);
            }

            return response()->json([
                'success' => false,
                'message' => 'No file provided'
            ], 400);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }
}

