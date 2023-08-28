<?php

namespace Melsaka\MediaFile\Controllers;

use Illuminate\Http\Request;
use Melsaka\MediaFile\MediaFile;
use Melsaka\MediaFile\Models\Media;
use Melsaka\MediaFile\Models\Folder;
use App\Http\Controllers\Controller;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        $folders = Folder::orderBy('order', 'asc')->get();

        $selectedFolder = Folder::where('slug', $request->folder)->first();

        $mediafiles = $selectedFolder ? $selectedFolder->mediafiles()->orderBy('id', 'desc')->paginate(30)->withQueryString() : Media::with('folder')->orderBy('id', 'desc')->paginate(30);

        return view('MediaFile::media.mediafile', compact('folders', 'selectedFolder', 'mediafiles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'file'      =>  'required|file',
            'folder'    =>  'required|exists:folders,slug'
        ]);

        $folder = Folder::whereSlug($request->folder)->firstOrFail();

        $mediafile = new MediaFile($folder);

        $media = $mediafile->store($request->file('file'));

        return redirect()->route('mediafiles.index', ['folder' => $folder->slug])
            ->with('success', 'File has been uploaded successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Media  $media
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $media)
    {
        $request->validate([
            'folder'    => 'required|exists:folders,slug',
            'name'      => 'required',
            'alt'       => 'nullable',
            'title'     => 'nullable',
            'caption'   => 'nullable',
        ]);

        $folder = Folder::whereSlug($request->folder)->firstOrFail();

        $media = Media::findOrFail($media);

        $mediafile = new MediaFile($folder);

        $isFolder = $mediafile->isFolder($media);

        if (!$isFolder) {
            return redirect()->back()->WithErrors(['This media doesnt belong to the folder: '. $folder->name]);
        }

        $media = $mediafile->update($request, $media);

        return redirect()->route('mediafiles.index', ['folder' => $folder->slug])
            ->with('success', 'File has been updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Media  $media
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $media)
    {
        $request->validate([
            'folder'    =>  'required|exists:folders,slug'
        ]);

        $folder = Folder::whereSlug($request->folder)->firstOrFail();

        $media = Media::findOrFail($media);

        $mediafile = new MediaFile($folder);

        $isFolder = $mediafile->isFolder($media);

        if (!$isFolder) {
            return redirect()->back()->WithErrors(['This media doesnt belong to the folder: '. $folder->name]);
        }

        $mediafile->delete($media);

        return redirect()->route('mediafiles.index', ['folder' => $folder->slug])
            ->with('success', 'File has been deleted successfully.');
    }
}
