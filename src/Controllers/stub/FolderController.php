<?php

namespace App\Http\Controllers\MediaFile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Melsaka\MediaFile\Models\Folder;
use Melsaka\MediaFile\MediaFolder;

class FolderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required',
            'order' => 'required|integer',
        ]);

        list($name, $order) = [$request->name, $request->order];

        $mediafolder = new MediaFolder();

        $folder = $mediafolder->store($name, $order);

        return redirect()->route('mediafiles.index', ['folder' => $folder->slug])
            ->with('success', 'Folder created successfully.');
    }

    public function update(Request $request, $folder)
    {
        $folder = Folder::whereSlug($folder)->firstOrFail();

        $request->validate([
            'name'  => 'required',
            'slug'  => 'required|unique:folders,slug,' . $folder->id,
            'order' => 'required|integer',
        ]);

        $mediafolder = new MediaFolder();

        $folder = $mediafolder->update($request, $folder);

        return redirect()->route('mediafiles.index', ['folder' => $folder->slug])
            ->with('success', 'Folder updated successfully.');
    }

    public function destroy($folder)
    {
        $folder = Folder::whereSlug($folder)->firstOrFail();

        if ($folder->mediafiles()->first()) {
            return redirect()->back()->withErrors([
                'You can\'t delete folder: [' . $folder->name . ']. Remove all files in this folder to delete the folder itself.'
            ]);
        }

        $mediafolder = new MediaFolder();

        $mediafolder->delete($folder);

        return redirect()->route('mediafiles.index')
            ->with('success', 'Folder deleted successfully.');
    }
}
