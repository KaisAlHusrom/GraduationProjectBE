<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FilesFunctions
{
    public static function saveFile($file, $filesFolderName = null, $imagesFolderName = null) : string {
        $imageMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/bmp', 'image/webp']; // Add more if needed
        $videoMimeTypes = ['video/mp4', 'video/x-msvideo', 'video/x-matroska', 'video/mpeg', 'video/quicktime'];
        $archiveMimeTypes = [
            'application/zip',
            'application/x-rar',
            'application/x-zip-compressed',
            'multipart/x-zip',
            'application/x-rar-compressed',
            'application/vnd.rar',
            'application/x-7z-compressed',  // for 7z files
            'application/gzip',  // for gzip files
            'application/x-tar',  // for tar files
            'application/x-bzip2',  // for bzip2 files
            'application/x-compressed',  // for generic compressed files
            'application/x-compress',  // for generic compressed files
            'application/x-stuffit',  // for StuffIt files
            'application/x-ace-compressed',  // for Ace Archive files
            'application/x-alz-compressed',  // for ALZip files
            'application/x-arj',  // for ARJ files
            'application/x-cpio',  // for cpio Archive files
            'application/x-lzh',  // for LZH Archive files
            'application/x-lzma',  // for LZMA Archive files
            'application/x-lzop',  // for LZOP Archive files
            'application/x-rpm',  // for RPM Package files
            'application/x-sgt',  // for StuffIt files
            'application/x-archive',  // for general archive files
            'application/x-gzip',  // for gzip files
            'application/x-z',  // for Z compressed files
        ];

        $storagePath = "public/assets";

        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $mimeType = $file->getMimeType();
        // Debugging the MIME type
        Log::info("File MIME type: " . $mimeType);

        if (in_array($mimeType, $archiveMimeTypes)) {
            $storagePath .= "/files/".$filesFolderName;
        } elseif (in_array($mimeType, $imageMimeTypes)) {
            $storagePath .= "/images/".$imagesFolderName;
        } elseif (in_array($mimeType, $videoMimeTypes)) {
            $storagePath .= "/videos/".$imagesFolderName;
        }

        //TODO: adding checking malicious files if there in zip file
        $file->storeAs($storagePath, $fileName);
        return $fileName;
    }

    public static function deleteFile(string $fileName, string $imagesFolderName = null, string $filesFolderName = null): void
    {
        // Check if the record has an associated file
        if ($fileName != null) {

            // Assuming $fileName contains only the name of the file
            $imagePath = "public/assets/images/$imagesFolderName/$fileName";
            $filePath = "public/assets/files/$filesFolderName/$fileName";
            $videoPath = "public/assets/videos/$imagesFolderName/$fileName";

            if (Storage::exists($imagePath)) {
                Log::info("Image path: " . $imagePath);
                // Delete the associated image
                Storage::delete($imagePath);
            }

            if (Storage::exists($filePath)) {
                Log::info("File path: " . $filePath);
                // Delete the associated file
                Storage::delete($filePath);
            }

            Log::info("Video path outside: " . $videoPath);
            if (Storage::exists($videoPath)) {
                Log::info("Video path inside: " . $videoPath);
                // Delete the associated video
                Storage::delete($videoPath);
            }
        }
    }
}
