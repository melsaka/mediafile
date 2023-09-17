<?php

namespace Melsaka\MediaFile;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Melsaka\MediaFile\Models\Folder;

class MediaFolder
{
    public function store($name, $order)
    {
        $slug           = Str::slug($name);

        $uniqueSlug     = $this->generateUniqueFolderSlug($slug);

        $storagePath    = Folder::createPath($uniqueSlug);

        $folderUri      = Folder::createUri($uniqueSlug);

        is_dir($storagePath) ?: @mkdir($storagePath, 0777, true);

        $folder = Folder::create([
            'name'          => $name,
            'slug'          => $uniqueSlug,
            'order'         => $order,
            'uri'           => $folderUri,
        ]);

        return $folder;
    }

    public function update(Request $request, Folder $folder)
    {
        list($uniqueSlug, $folderUri) = [$request->slug, $folder->uri];

        if ($this->isSlugChanged($folder, $uniqueSlug)) {
            $uniqueSlug     = $this->generateUniqueFolderSlug($uniqueSlug);

            $newStoragePath = Folder::createPath($uniqueSlug);
            $folderUri      = Folder::createUri($uniqueSlug);

            static::renameDirectory($folder->getStoragePath(), $newStoragePath);

            $folder->mediafiles()->update([
                'uri' => DB::raw("REPLACE(uri, '$folder->uri', '$folderUri')")
            ]);
        }

        $folder->update([
            'name'          => $request->name,
            'order'         => $request->order,
            'slug'          => $uniqueSlug,
            'uri'           => $folderUri,
        ]);

        return $folder;
    }

    public function delete(Folder $folder)
    {
        static::deleteDirectory($folder->getStoragePath());

        $folder->mediafiles()->delete();

        return $folder->delete();
    }

    private function generateUniqueFolderSlug($slug)
    {
        $steps = 20;

        $start = 1;

        $limit = $steps;

        while($this->checkIfFolderExists($slug)) {
            $slugs = $this->createFolderSlugs($slug, $start, $limit);

            $availableSlugs = $this->filterAvailableSlugs($slugs);

            if ($availableSlugs->count()) {
                $slug = $availableSlugs->first()['slug'];
            }

            $start = $start + $steps;

            $limit = $limit + $steps;
        }

        return $slug;
    }

    private function checkIfFolderExists($slug)
    {
        $folder = Folder::where('slug', $slug)->first();

        return (bool) $folder;
    }

    private function createFolderSlugs($slug, $start, $limit)
    {
        $slugs = collect([]);

        for ($i = $start; $i <= $limit; $i++) {
            $newSlug = $slug . '-' . $i;

            $slugs->push([
                'slug' => $newSlug,
            ]);
        }

        return $slugs;
    }

    private function filterAvailableSlugs($slugs)
    {
        $newSlugsList = $slugs->pluck('slug')->toArray();

        $foldersHaveSameSlug = Folder::whereIn('slug', $newSlugsList)->get()->pluck('slug');

        $availableSlugs = $slugs->whereNotIn('slug', $foldersHaveSameSlug);

        return $availableSlugs;
    }

    private function isSlugChanged($folder, $requestSlug)
    {
        return $requestSlug !== $folder->slug;
    }

    public static function renameDirectory($oldFolderPath, $newFolderPath)
    {

        if (file_exists($oldFolderPath) && is_dir($oldFolderPath)) {
            rename($oldFolderPath, $newFolderPath);
            return true;
        }

        return false;
    }

    public static function deleteDirectory($dirPath)
    {
        if (!is_dir($dirPath)) {
            return;
        }

        $dirHandle = opendir($dirPath);

        while (($file = readdir($dirHandle)) !== false) {
            if ($file != '.' && $file != '..') {
                $filePath = $dirPath . '/' . $file;

                if (is_dir($filePath)) {
                    static::deleteDirectory($filePath); // Recursive call for subdirectories
                } else {
                    unlink($filePath); // Delete individual files
                }
            }
        }

        closedir($dirHandle);
        rmdir($dirPath); // Remove the directory itself
    }
}
