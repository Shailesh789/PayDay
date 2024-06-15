<?php


namespace App\Models\Core\File\Traits;


use App\Helpers\Core\Traits\FileHandler;
use Illuminate\Support\Facades\Storage;

trait FileAttribute
{
    use FileHandler;

    public function getFullUrlAttribute()
    {
        $file_system = config('filesystems.default');

        $path = $this->removeStorage($this->path);

        if (in_array($file_system, ['local', 'public'])) {
            return request()->root() . (Storage::disk($file_system)->url($path));
        }
        return Storage::disk($file_system)->url($path);

//        if (in_array(config('filesystems.default'), ['local', 'public'])) {
//            return request()->root().$this->path;
//        }
//        return $this->path;
//        $system = config('filesystems.default');
//        return  Storage::disk($system)->url($this->path);
    }
}
