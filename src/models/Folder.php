<?php

namespace Melsaka\MediaFile\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Melsaka\MediaFile\Models\Media;

class Folder extends Model
{
    use HasFactory;

    // Settings

    protected $fillable = [
        'name',
        'slug',
        'uri',
        'order',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }
    
    // Relationships

    public function mediafiles()
    {
        return $this->hasMany(Media::class);
    }

    // Helpers

    public static function createPath($uniqueSlug)
    {
        $mediaFolder = config('mediafile.folder_name');

        $storagePublicPath = storage_path('app/public/');

        if ($mediaFolder) {
            $storagePublicPath = $storagePublicPath . $mediaFolder . '/';
        }

        return $storagePublicPath . $uniqueSlug . '/';
    }

    public static function createUri($uniqueSlug)
    {
        $mediaFolder = config('mediafile.folder_name');

        $mainFolderPath = 'storage/';

        if ($mediaFolder) {
            $mainFolderPath = $mainFolderPath . $mediaFolder .'/';
        }

        return $mainFolderPath . $uniqueSlug . '/';
    }

    public function getStoragePath()
    {
        return Folder::createPath($this->slug);
    }
}
