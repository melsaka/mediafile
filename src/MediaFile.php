<?php

namespace Melsaka\MediaFile;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Melsaka\MediaFile\Models\Media;
use Melsaka\MediaFile\Models\Folder;
use Illuminate\Database\Eloquent\Model;
use Melsaka\MediaFile\Helpers\FileHandlers;
use Melsaka\MediaFile\Helpers\ImageFileHandler;
use Intervention\Image\ImageManagerStatic as Image;

class MediaFile
{
    use FileHandlers;
    use ImageFileHandler;

    public $folder;

    public $testing;

    public function __construct(Folder $folder, $testing = false)
    {
        $this->folder = $folder;

        $this->testing = $testing;
    }

    public function store(UploadedFile $uploadedFile)
    {
        $isImage = Media::isFileAnImage($uploadedFile->getClientMimeType());

        if ($isImage) {
            $imageFile = Image::make($uploadedFile);
        }

        $fileName = $this->getFileName($uploadedFile);

        $fileExtension = $uploadedFile->getClientOriginalExtension();

        $fileEncoder = config('mediafile.image_encoder');

        list($fileName, $fileUri, $fileStoragePath) = $this->getUniqueFileData($fileName, $fileExtension);

        $thumbnails = Media::getImageThumbnailsConfigurations();

        $userId = $this->testing ? 0 : auth()->id();

        $media = Media::create([
            'user_id'       => $userId,
            'folder_id'     => $this->folder->id,
            'name'          => $fileName,
            'extension'     => $fileExtension,
            'encoder'       => $fileEncoder,
            'type'          => $uploadedFile->getClientMimeType(),
            'uri'           => $fileUri,
            'size'          => $uploadedFile->getSize(),
            'width'         => $isImage ? $imageFile->width() : null, // Intervenion Image
            'height'        => $isImage ? $imageFile->height() : null, // Intervenion Image
            'thumbnails'    => $isImage ? $thumbnails : null,
        ]);

        if ($isImage) {
            $this->createImageFile($imageFile, $fileStoragePath, $fileName);

            return $media;
        }

        $filename = basename($fileStoragePath);

        $storagePath = str_replace(storage_path('app'), '', dirname($fileStoragePath));

        $uploadedFile->storeAs($storagePath, $filename);

        return $media;
    }

    public function update(Request $request, Media $media)
    {
        $newFileName = $request->name;

        if($this->isNameChanged($media, $request->name)) {
            list($newFileName, $newFileUri) = $this->updateFileName($media, $request->name);
            $media->uri = $newFileUri;
        }

        $media->name    = $newFileName;
        $media->alt     = $request->alt;
        $media->title   = $request->title;
        $media->caption = $request->caption;
        $media->save();

        return $media;
    }

    public function delete(Media $media)
    {
        if (file_exists($media->getStoragePath())) {
            unlink($media->getStoragePath());
        }

        if ($media->isFileAnImage()) {
            $this->deleteRelatedImageFiles($media);
        }

        return $media->delete();
    }

    public function isFolder(Media $media)
    {
        return $media->folder_id === $this->folder->id;
    }
}
