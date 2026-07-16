<?php

namespace App\Services;

use App\Traits\FileUploadTrait;
use Illuminate\Http\Request;

/**
 * Injectable wrapper around the legacy FileUploadTrait so services and
 * controllers can depend on an abstraction instead of mixing in the trait.
 */
class ImageUploadService
{
    use FileUploadTrait;

    public function upload(Request $request, string $inputName, ?string $oldPath = null, string $path = '/uploads'): ?string
    {
        return $this->uploadImage($request, $inputName, $oldPath, $path);
    }
}
