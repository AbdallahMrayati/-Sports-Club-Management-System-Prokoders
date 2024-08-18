<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Media;

if (!function_exists('upload_media')) {
    /**
     * Upload media files and save their information to the database.
     *
     * @param Request $request
     * @param string $fileInputName
     * @param int $sportId
     * @return Media[]|null
     */
    function upload_media(Request $request, $fileInputName, $sportId)
    {
        $mediaRecords = [];

        if ($request->hasFile($fileInputName)) {
            $files = $request->file($fileInputName);

            // Ensure $files is an array
            if (!is_array($files)) {
                $files = [$files];
            }

            foreach ($files as $file) {
                // Generate a unique file name with the original extension
                $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();

                // Determine the media type (image or video)
                $mediaType = $file->getClientMimeType();
                if (str_starts_with($mediaType, 'image/')) {
                    $type = 'image';
                } elseif (str_starts_with($mediaType, 'video/')) {
                    $type = 'video';
                } else {
                    // Skip files that do not meet the media type criteria
                    continue;
                }

                // Store the file and get the path
                $path = $file->storeAs('media', $filename, 'public');

                // Create a Media record and add it to the array
                $media = new Media();
                $media->sport_id = $sportId;
                $media->type = $type;
                $media->file_path = $path;
                $media->save();

                $mediaRecords[] = $media;
            }
        }

        return $mediaRecords;
    }
}