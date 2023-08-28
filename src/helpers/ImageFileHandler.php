<?php

namespace Melsaka\MediaFile\Helpers;

use Melsaka\MediaFile\Models\Media;

trait ImageFileHandler
{
    private function createImageFile($imageFile, $fileStoragePath, $fileName)
    {
        $config         = config('mediafile');

        $imageEncoder   = $config['image_encoder'];
        
        $separator      = $config['image_thumbnail_separator'];

        $imageSizes     = $config['image_thumbnail_sizes'];

        $imageFile->save($fileStoragePath);

        $fileDirPath = dirname($fileStoragePath);

        $imageFile->encode($imageEncoder)->save($fileDirPath . '/' . $fileName . '.' . $imageEncoder);

        foreach ($imageSizes as $imageSize) {
            $path = $fileDirPath . '/' . $fileName . $separator . $imageSize . '.' . $imageEncoder;

            $imageFile->encode($imageEncoder)->resize($imageSize, null, function ($constraint) {
                $constraint->aspectRatio();
            })->save($path);
        }
    }

    private function updateImageFiles($media, $newFileName, $fileFolderPath)
    {
        $encodedImageUri = $fileFolderPath . $newFileName . '.' . $media->encoder;

        if (file_exists($media->getEncodedImagePath())) {
            rename($media->getEncodedImagePath(), Media::getStoragePath($encodedImageUri));
        }

        foreach ($media->getThumbnails() as $thumbnail) {

            $exploded = explode($media->name, $thumbnail);

            $newThumbnail = implode($newFileName, $exploded);

            if (file_exists(Media::getStoragePath($thumbnail))) {
                rename(Media::getStoragePath($thumbnail), Media::getStoragePath($newThumbnail));
            }
        }
    }

    private function deleteRelatedImageFiles($media)
    {
        if(file_exists($media->getEncodedImagePath())) {
            unlink($media->getEncodedImagePath());
        }

        foreach ($media->getThumbnails() as $thumbnail) {
            if (file_exists(Media::getStoragePath($thumbnail))) {
                unlink(Media::getStoragePath($thumbnail));
            }
        }
    }
}
