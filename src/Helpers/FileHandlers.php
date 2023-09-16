<?php

namespace Melsaka\MediaFile\Helpers;

use Illuminate\Support\Str;
use Melsaka\MediaFile\Models\Media;

trait FileHandlers
{
    private function getUniqueFileData($fileName, $fileExtension)
    {
        $subfolder = $this->createYearMonthFoldersIn($this->folder->getStoragePath()); // 2023/8

        $fileFolderPath = $this->folder->uri . $subfolder;

        $fileName = $this->makeSureFileNameIsUnique($fileFolderPath, $fileName, $fileExtension);

        $fileFullName = $fileName . '.' . $fileExtension;

        $fileUri = $fileFolderPath . $fileFullName;

        $fileStoragePath = $this->folder->getStoragePath() . $subfolder . $fileFullName;

        return [$fileName, $fileUri, $fileStoragePath];
    }

    private function createYearMonthFoldersIn($folderPath)
    {
        $yearMonth = explode('/', now()->format('Y/m'));

        $yearFolderPath = $folderPath . $yearMonth[0] . '/';

        $monthFolderPath = $folderPath . $yearMonth[0] . '/' . $yearMonth[1] . '/';

        is_dir($yearFolderPath) ?: @mkdir($yearFolderPath, 0777, true);

        is_dir($monthFolderPath) ?: @mkdir($monthFolderPath, 0777, true);

        return $yearMonth[0] . '/' . $yearMonth[1] . '/';
    }
    
    private function makeSureFileNameIsUnique($fileFolderPath, $fileName, $fileExtension)
    {
        $data = [
            'folder_path'   => $fileFolderPath,
            'name'          => $fileName,
            'extension'     => $fileExtension,
        ];

        $steps = 20;

        $start = 1;

        $limit = $steps;

        while($this->checkIfFileExists($data)) {
            $names = $this->createFileNames($data, $start, $limit);

            $availableNames = $this->filterAvailableNames($names);

            if ($availableNames->count()) {
                $data['name'] = $availableNames->first()['name'];
            }

            $start = $start + $steps;

            $limit = $limit + $steps;
        }

        return $data['name'];
    }

    private function checkIfFileExists($data)
    {
        $fileUri =  $data['folder_path'] . $data['name'] . '.' . $data['extension'];

        $file = Media::where('uri', $fileUri)->first();

        return (bool) $file;
    }

    private function createFileNames($data, $start, $limit)
    {
        $names = collect([]);

        for ($i = $start; $i <= $limit; $i++) {
            $name = $data['name'] . '-' . $i;

            $names->push([
                'name' => $name,
                'uri' => $data['folder_path'] . $name . '.' . $data['extension'],
            ]);
        }

        return $names;
    }

    private function filterAvailableNames($names)
    {
        $newUriList = $names->pluck('uri')->toArray();

        $mediaHasSameUris = Media::whereIn('uri', $newUriList)->get()->pluck('uri');

        $availableNames = $names->whereNotIn('uri', $mediaHasSameUris);

        return $availableNames;
    }

    private function isNameChanged($media, $requestName)
    {
        return $requestName !== $media->name;
    }

    private function updateFileName($media, $name)
    {
        $isImage = $media->isFileAnImage();

        $fileFolderPath = dirname($media->uri) . '/';

        $requestName = Str::slug($name);

        $newFileName = $this->makeSureFileNameIsUnique($fileFolderPath, $requestName, $media->extension);

        $newFileUri = $fileFolderPath . $newFileName . '.' .$media->extension;

        if (file_exists($media->getStoragePath())) {
            rename($media->getStoragePath(), Media::getStoragePath($newFileUri));
        }

        if($isImage) {
            $this->updateImageFiles($media, $newFileName, $fileFolderPath);
        }

        return [$newFileName, $newFileUri];
    }

    private function getFileName($file)
    {
        $fileNameWithoutExtension = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        $slug = Str::slug($fileNameWithoutExtension);

        return mb_substr($slug, 0, 75);
    }
}
