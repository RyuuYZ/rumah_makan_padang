<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class WebpUploadService
{
    /**
     * Upload an image file, convert it to WebP format if necessary, and save to public directory.
     *
     * @param  string  $folder  Relative path under public/ (default: 'menu')
     * @param  int  $quality  Quality 0-100 (default: 82)
     * @return string Relative public path ending with .webp (e.g. '/menu/nama-item-12345.webp')
     */
    public function uploadAndConvertToWebp(UploadedFile $file, string $folder = 'menu', int $quality = 82): string
    {
        $folderPath = trim($folder, '/');
        $destinationPath = public_path($folderPath);

        if (! file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $basename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        if (empty($basename)) {
            $basename = 'image';
        }

        $filename = $basename.'-'.time().'-'.Str::random(4).'.webp';
        $fullPath = $destinationPath.'/'.$filename;

        $mimeType = $file->getMimeType();
        $realPath = $file->getRealPath();
        $extension = strtolower($file->getClientOriginalExtension());

        // If file is already a WebP image, move it directly
        if ($mimeType === 'image/webp' || $extension === 'webp') {
            $file->move($destinationPath, $filename);

            return '/'.$folderPath.'/'.$filename;
        }

        // Convert image to WebP using PHP GD if available
        $image = null;
        switch ($mimeType) {
            case 'image/jpeg':
            case 'image/jpg':
            case 'image/pjpeg':
                $image = @imagecreatefromjpeg($realPath);
                break;
            case 'image/png':
            case 'image/x-png':
                $image = @imagecreatefrompng($realPath);
                if ($image) {
                    imagepalettetotruecolor($image);
                    imagealphablending($image, false);
                    imagesavealpha($image, true);
                }
                break;
            case 'image/gif':
                $image = @imagecreatefromgif($realPath);
                break;
            case 'image/bmp':
            case 'image/x-ms-bmp':
                $image = @imagecreatefrombmp($realPath);
                break;
        }

        if ($image && function_exists('imagewebp')) {
            imagewebp($image, $fullPath, $quality);
            imagedestroy($image);

            return '/'.$folderPath.'/'.$filename;
        }

        // Fallback: move file directly if GD conversion isn't possible
        $fallbackFilename = $basename.'-'.time().'.'.$file->extension();
        $file->move($destinationPath, $fallbackFilename);

        return '/'.$folderPath.'/'.$fallbackFilename;
    }
}
