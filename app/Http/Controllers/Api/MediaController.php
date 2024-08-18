<?php

namespace App\Http\Controllers\API;

use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id, $sportId)
    {
        // Find the media record by its ID
        $media = Media::where('id', $id)->where('sport_id', $sportId)->first();

        if (!$media) {
            return response()->json([
                'error' => 'Media not found for the specified sport.',
            ], 404);
        }

        // Delete the file from storage
        Storage::disk('public')->delete($media->file_path);

        // Delete the media record from the database
        $media->delete();

        return response()->json([
            'message' => 'Media deleted successfully.',
        ]);
    }
}