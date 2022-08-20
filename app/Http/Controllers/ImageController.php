<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;

class ImageController extends Controller
{
    // For Picture Upload. Logo, Profile, etc.
    public function store(Request $request, String $location): JsonResponse
    {
        Log::info('ImageController Store');
        $data = request()->validate([
            'image' => 'required',
        ]);
        $hashName = $data['image']->hashName();

        $image = Image::make($data['image']);

        $image->save(storage_path("app/public/$location/$hashName"));

        return response()->json([
            'data' => [
                'filename' => $hashName,
            ],
            'links' => [
                'self' => asset("storage/$location/$hashName"),
            ]
        ]);
    }
}
