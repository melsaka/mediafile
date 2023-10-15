<?php

namespace Melsaka\MediaFile;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Melsaka\MediaFile\MediaFolder;
use Melsaka\MediaFile\Models\Media;
use Melsaka\MediaFile\Models\Folder;
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

    public static function createUploadImageFromUrl($imageUrl)
    {
        $httpClient = new Client();
        $response = $httpClient->get($imageUrl);

        // Get the MIME type from the Content-Type header
        $contentType = $response->getHeaderLine('Content-Type');
        // Use the custom function to get the file extension
        $fileExtension = static::getExtensionFromMimeType($contentType);

        if (!$fileExtension) {
            // Handle the case where the extension cannot be determined
            throw new Exception('Unable to determine file extension.');
        }

        $tempFolder = storage_path('app/media-temps/');

        is_dir($tempFolder) ?: @mkdir($tempFolder, 0777, true);

        $temporaryPath = $tempFolder. 'image_'.uniqid().'.'. $fileExtension;

        file_put_contents($temporaryPath, $response->getBody());

        // Define a destructor function to remove the temporary file when it's no longer needed
        register_shutdown_function(function () use ($tempFolder) {
            if (is_dir($tempFolder)) {
                MediaFolder::deleteDirectory($tempFolder);
            }
        });

        $uploadedFile = new UploadedFile(
            $temporaryPath, // The path to the temporary image file
            basename($temporaryPath), // The original file name (can be different)
            mime_content_type($temporaryPath), // The MIME type of the file
            filesize($temporaryPath), // The file size
            UPLOAD_ERR_OK, // The error code (0 for no error)
            true // Indicates that the file should be moved to the storage directory
        );

        return $uploadedFile;
    }

    private static function getExtensionFromMimeType($mimeType)
    {
        $mimeToExtensionMapping = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/gif'  => 'gif',
            'image/bmp'  => 'bmp',
            'image/webp' => 'webp'
        ];

        return $mimeToExtensionMapping[$mimeType] ?? null;
    }

    private static function isPublished($controller)
    {
        return file_exists(app_path('Http/Controllers/MediaFile/'.$controller.'.php'));
    }

    private static function ifPublished($controller)
    {
        return self::isPublished($controller) ? '\\App\\Http\\Controllers\\MediaFile\\'. $controller : '\\Melsaka\\MediaFile\\Controllers\\'. $controller;
    }

    public static function routes($name = 'mediafiles')
    {
        $folderController = self::ifPublished('FolderController');
        $mediaController =  self::ifPublished('MediaController');

        \Illuminate\Support\Facades\Route::resource('mediafiles-folders', $folderController)->only(['store', 'update', 'destroy']);

        \Illuminate\Support\Facades\Route::resource($name, $mediaController)
                                        ->only(['index', 'store', 'update', 'destroy'])
                                        ->names([
                                            'index' => 'mediafiles.index',
                                            'store' => 'mediafiles.store',
                                            'update' => 'mediafiles.update',
                                            'destroy' => 'mediafiles.destroy',
                                        ]);
    }
}
