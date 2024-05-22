<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FilesFunctions
{
    public static function saveFile($file, $filesFolderName = null, $imagesFolderName = null) : string {
        $imageMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/bmp', 'image/webp']; // Add more if needed
        $storagePath = "public/assets";

        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $mimeType = $file->getMimeType();
        // Debugging the MIME type


        if($mimeType === 'application/zip') {
            $storagePath .= "/files/".$filesFolderName;
        }

        if(in_array($mimeType, $imageMimeTypes)) {

            $storagePath .= "/images/".$imagesFolderName;
        }
        //TODO: adding checking malicious files if there in zip file
        $file->storeAs($storagePath, $fileName);
        return $fileName;
    }

    public static function deleteFile(string $fileName, string $imagesFolderName = null, string $filesFolderName = null): void
    {
        // Check if the record has an associated image
        if ($fileName != null) {

            // Assuming $fileName contains only the name of the file
            $imagePath = "public/assets/images/$imagesFolderName/$fileName";

            $filePath = "public/assets/files/$filesFolderName/$fileName";
            if (Storage::exists($imagePath)) {
                Log::info("image path Type: " . $imagePath);
                // Delete the associated image
                Storage::delete($imagePath);
            }

            if (Storage::exists($filePath)) {
                // Delete the associated file
                Storage::delete($filePath);
            }

        }
    }
}
