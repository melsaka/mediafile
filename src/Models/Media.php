<?php

namespace Melsaka\MediaFile\Models;

use Illuminate\Database\Eloquent\Model;
use Melsaka\MediaFile\Models\Folder;

class Media extends Model
{
    // Settings

    protected $fillable = [
        'user_id',
        'folder_id',
        'name',
        'extension',
        'encoder',
        'type',
        'uri',
        'size',
        'width',
        'height',
        'alt',
        'title',
        'caption',
        'thumbnails',
    ];

    // Relationships

    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }

    // Helpers

    public function getFileLink()
    {
        return '/'. $this->uri;
    }

    public function getEncodedImageLink()
    {
        $uri = str_replace($this->getFileName(), $this->getFileName(true), $this->uri);

        return '/' . $uri;
    }

    public function getFileName($encoded = false)
    {
        return $encoded ? $this->name . '.' .$this->encoder : $this->name . '.' .$this->extension;
    }

    public function getFileSize()
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $unit = 0;

        while ($this->size >= 1024 && $unit < count($units) - 1) {
            $this->size /= 1024;
            $unit++;
        }

        return number_format($this->size, ($unit == 0) ? 0 : 2) . ' ' . $units[$unit];
    }

    public static function getImageThumbnailsConfigurations()
    {
        $data = [
            'sizes'     => config('mediafile.image_thumbnail_sizes'),
            'separator' => config('mediafile.image_thumbnail_separator')
        ];

        return json_encode($data);
    }

    public function getThumbnails($name = null, $onlySizes = true)
    {
        if (!$this->isFileAnImage()) {
            return [];
        }

        $imageThumbnails = json_decode($this->thumbnails, true);

        $thumbnails = [];
        $thumbnails['separator'] = $imageThumbnails['separator'];

        $dirPath = dirname($this->uri) . '/';

        foreach ($imageThumbnails['sizes'] as $thumbanilName => $size) {
            $thumbnails['sizes'][$size] = $dirPath . $this->name . $thumbnails['separator'] . $size . '.'. $this->encoder;

            if ($thumbanilName === $name) {
                return $thumbnails['sizes'][$size];
            }
        }

        if ($name !== null) {
            return $this->getEncodedImageLink();
        }

        return $onlySizes ? $thumbnails['sizes'] : $thumbnails;
    }

    public function getSrcset()
    {
        $thumbnails = $this->getThumbnails();

        $thumbnails = array_reverse($thumbnails, true);

        $srcset = '';

        foreach($thumbnails as $size => $uri) {
            $srcset = $srcset . '/' . $uri . ' ' . $size . 'w, ';
        }

        $srcset = $srcset . $this->getEncodedImageLink() . ' ' . $this->width . 'w';

        return $srcset;
    }

    public function getSizes()
    {
        $thumbnails = $this->getThumbnails();

        $thumbnails = array_reverse($thumbnails, true);

        $sizes = '';

        foreach($thumbnails as $size => $uri) {
            $sizes = $sizes . '(max-width: '. $size .'px) ' . $size . 'px, ';
        }

        $sizes = $sizes . $this->width . 'px';

        return $sizes;
    }

    public function scopeIsFileAnImage($query, $mimeType = '')
    {
        $mimeType = $mimeType ?: $this->type;
        return preg_match('/^image\//', $mimeType);
    }

    public function getSubFolder()
    {
        return $this->created_at->format('Y/m') . '/';
    }

    public function scopeGetStoragePath($query, $imageUri = null)
    {
        $imageUri = $imageUri ?: $this->uri;

        $uri = mb_substr($imageUri, strlen('storage/'));

        return storage_path('app/public/'. $uri);
    }

    public function getEncodedImagePath()
    {
        return dirname($this->getStoragePath()) . '/' . $this->name  . '.' . $this->encoder;
    }
}
