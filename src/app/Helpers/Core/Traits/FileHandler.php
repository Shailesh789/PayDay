<?php


namespace App\Helpers\Core\Traits;


use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

trait FileHandler
{
    protected string $storagePrefix = 'public/';
    protected bool $isOriginalName = false;

    public function storeFile(UploadedFile $file, $folder = 'avatar'): string
    {
        $name = $this->generateUploadingFileName($file);
        $file->storeAs($folder, $name);

        //        return $this->storageDisk()->url($folder . '/' . $name);
        return "$folder/$name";
    }

    private function generateUploadingFileName(UploadedFile $file): string
    {
        $name = $this->getDefaultName();

        if ($this->isOriginalName) {
            $name = Str::of($file->getClientOriginalName())
                ->replaceMatches("/[.].*/", '')
                ->snake()
                ->limit(30, '');
            $name = $name . "-" . uniqid();
        }

        return $name . "." . $file->getClientOriginalExtension();
    }

    private function getDefaultName(): string
    {
        return Str::random(40);
    }

    public function uploadImage(UploadedFile $uploadedFile = null, $folder = "logo", $height = 300): ?string
    {
        if (is_null($uploadedFile))
            return null;
        $file = $this->saveImage($uploadedFile, $folder, $height);
        if ($file->success)
            return $file->path;
        return null;
    }

    public function saveImage(UploadedFile $file, $subdirectory = 'logo', $height = 300): object
    {
        $file_name = uniqid() . '.' . $file->getClientOriginalExtension();
        $file_path = $subdirectory . '/' . $file_name;
        try {
            $file = $this->makeImage($file, $height)->stream(); //resizing image
        } catch (Exception $exception) {
            $this->storageDisk()->putFileAs($subdirectory, $file, $file_name);
            return (object)["success" => true, "message" => "File has been uploaded successfully", "path" => $file_path];
        }


        $this->storageDisk()->put($file_path, $file);
        return (object)["success" => true, "message" => "File has been uploaded successfully", "path" => $file_path];
    }

    public function makeImage(UploadedFile $file, $height = 300): \Intervention\Image\Image
    {
        $extension = $file->getClientOriginalExtension();

        return Image::make($file)->resize(null, $height, function ($constraint) {
            $constraint->aspectRatio();
        })->encode($extension)->save();
    }

    public function storageDisk(): \Illuminate\Contracts\Filesystem\Filesystem
    {
        return Storage::disk(config('filesystems.default'));
    }

    public function deleteImage(string $path = null): bool
    {
        //        if ($this->isImageDirectory($path)){
        //            return false;
        //        }
        return $this->deleteFile($path);
    }

    public function deleteFile(string $path = null): bool
    {
        $path = $this->removeStorage($path);
        if ($path && $this->isFile($path)) {
            return $this->storageDisk()->delete($path);
        }
        return false;
    }

    public function removeStorage($path): string
    {
        return str_replace('/storage', '', $path);
    }

    public function isFile(string $path = null): bool
    {
        return $this->storageDisk()->exists($path);
    }

    public function isImageDirectory(string $path = null): bool
    {
        return Str::contains($path, 'images/');
    }

    public function deleteMultipleFile($paths)
    {
        foreach ($paths as $path) {
            $this->deleteFile($path);
        }

        return true;
    }

    public function filePath(string $path = null): ?string
    {
        if ($path && $this->isFile($path))
            return $this->storageDisk()->url($path);
        return null;
    }

    private function isWithOriginalName($flag = true)
    {
        $this->isOriginalName = $flag;
        return $this;
    }
}
