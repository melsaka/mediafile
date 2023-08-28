@extends('MediaFile::master')

@section('content')
<div class="pt-8">
 <h2 class="text-lg font-medium mr-auto">
     File Manager
 </h2>
</div>

<div class="grid grid-cols-12 gap-6 mt-5">
 <div class="col-span-12 flex flex-wrap sm:flex-nowrap justify-between mt-2">
    @if($selectedFolder)
    <form action="{{ route('mediafiles.store') }}" method="POST" class="flex" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="folder" value="{{ $selectedFolder->slug }}">
        <input class="rounded-lg mr-2 bg-dash-400 focus:ring-4 focus:ring-gray-700/[.7] border-none text-sm text-gray-300 mb-2" name="file" type="file" accept="image/*">

        <button class="text-white bg-blue-800/[.8] hover:bg-blue-700/[.8] focus:ring-4 focus:ring-blue-300 font-medium rounded-md text-sm px-4 py-2 focus:outline-none mb-2">Upload New File</button>
    </form>
    @endif
    <div class="w-auto mt-0 ">
        <div class="w-56 relative text-gray-500">
            <input class="w-full py-2 pr-10 w-56 pl-4 rounded-lg bg-dash-400 focus:ring-4 focus:ring-gray-700/[.7] border-none text-sm text-gray-300 mb-2" placeholder="Search files..." type="text" name="q">
            <svg class="w-4 h-4 absolute inset-y-0 mt-2.5 mr-3 right-0" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="search" data-lucide="search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg> 
        </div>
    </div>
 </div>

 <div class="col-span-12 overflow-auto lg:overflow-visible">
    <div class="grid grid-cols-12 gap-6 text-gray-300 text-sm">
        <!-- content -->
        <div class="col-span-12 lg:col-span-3 2xl:col-span-2">
            <div class="bg-gray-800 rounded-md p-5">
                <div class="mt-1">
                    @if ($folders->count())
                    <div class="relative">
                        <a href="{{ route('mediafiles.index') }}" @if(!$selectedFolder) class="flex items-center px-3 py-2 mb-2 rounded-md bg-blue-800/[.8] text-white font-medium" @else class="flex items-center px-3 py-2 mb-2 rounded-md bg-dash-400 hover:bg-dash-200" @endif>All Files</a>
                    </div>
                    @endif
                    @foreach($folders as $folder)
                    <div class="relative">
                        <a href="{{ route('mediafiles.index') }}?folder={{ $folder->slug }}" @if($selectedFolder && $folder->id === $selectedFolder->id) class="flex items-center px-3 py-2 mb-2 rounded-md bg-blue-800/[.8] text-white font-medium" @else class="flex items-center px-3 py-2 mb-2 rounded-md bg-dash-400 hover:bg-dash-200" @endif>{{ $folder->name }}</a>
                        <div class="absolute top-0 right-0">
                            <button class="p-2" type="button" aria-hidden="true" data-dropdown-placement="right" data-dropdown-toggle="edit-folder-{{ $folder->id }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                </svg>
                            </button>
                        </div>
                        <div class="absolute top-0 left-0 z-40">
                            <div id="edit-folder-{{ $folder->id }}" class="hidden bg-dash-400 p-2 rounded-md text-xs" aria-labelledby="Folder {{ $folder->id }} edit panel">
                                <form style="width: 300px" action="{{ route('mediafiles-folders.update', $folder) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="relative">
                                        <span class="text-xs absolute mr-8 right-0 top-2 py-0.5 rounded px-2 bg-dash-200 opacity-0.6">Name</span>
                                        <input class="w-full py-2 px-4 rounded bg-dash-500 focus:ring-4 focus:ring-dash-300 border-none text-sm text-gray-300" placeholder="Name" type="text" name="name" value="{{ $folder->name }}" required>
                                    </div>

                                    <div class="relative mt-2">
                                        <span class="text-xs absolute mr-8 right-0 top-2 py-0.5 rounded px-2 bg-dash-200 opacity-0.6">Folder</span>
                                        <input class="w-full py-2 px-4 rounded bg-dash-500 focus:ring-4 focus:ring-dash-300 border-none text-sm text-gray-300" placeholder="Slug" type="text" name="slug" value="{{ $folder->slug }}" required>
                                    </div>

                                    <div class="relative mt-2">
                                        <span class="text-xs absolute mr-8 right-0 top-2 py-0.5 rounded px-2 bg-dash-200 opacity-0.6">Order</span>
                                        <input class="w-full py-2 px-4 font-semibold rounded pr-12 bg-dash-500 focus:ring-4 focus:ring-dash-300 border-none text-sm text-gray-300" placeholder="Category Order" type="number" name="order" value="{{ $folder->order }}">
                                    </div>

                                    <button type="submit" class="w-full text-white bg-blue-800/[.8] hover:bg-blue-700/[.8] focus:ring-4 focus:ring-blue-300 font-medium rounded text-xs px-4 py-2 mt-2 focus:outline-none">Update</button>
                                </form>
                                <form style="width: 300px" action="{{ route('mediafiles-folders.destroy', $folder) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full text-white bg-red-800/[.8] hover:bg-red-700/[.8] focus:ring-4 focus:ring-dash-300 font-medium rounded text-xs px-4 py-2 mt-2 focus:outline-none" onclick="return confirm('Are you sure that you wanna delete this media folder?');">Delete</button>
                                </form>
                            </div>
                        </div>

                    </div>
                    @endforeach

                    
                    <div class="relative">
                        <a href="javascript:;" aria-hidden="true" data-dropdown-placement="bottom" data-dropdown-toggle="new-folder" class="flex items-center px-3 py-2 rounded-md bg-dash-400 hover:bg-dash-200"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="plus" class="lucide lucide-plus w-4 h-4 mr-2" data-lucide="plus"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg> Add New Label </a>

                        <div class="absolute top-0 left-0 z-40">
                            <div id="new-folder" class="hidden bg-dash-300 p-2 rounded-md text-xs" aria-labelledby="New Folder Creator">
                                <form style="width: 300px" action="{{ route('mediafiles-folders.store') }}" method="POST">
                                    @csrf
                                    <input class="w-full py-2 px-4 rounded bg-dash-500 focus:ring-4 focus:ring-dash-300 border-none text-sm text-gray-300" placeholder="Name" type="text" name="name" value="{{ old('name') }}" required>

                                    <div class="relative">
                                        <input class="w-full py-2 px-4 mt-2 font-semibold rounded pr-10 bg-dash-500 focus:ring-4 focus:ring-dash-300 border-none text-sm text-gray-300" placeholder="Category Order" type="number" name="order" value="{{ old('order') ?? 1 }}">
                                    </div>

                                    <button type="submit" class="w-full text-white bg-blue-800/[.8] hover:bg-blue-700/[.8] focus:ring-4 focus:ring-blue-300 font-medium rounded text-xs px-4 py-2 mt-2 focus:outline-none">Add Label</button>

                                </form>
                            </div>
                        </div>
                        
                    </div>

                </div>
            </div>
        </div>
        <div class="col-span-12 lg:col-span-9 2xl:col-span-10">
            <div class="grid grid-cols-12 gap-3 sm:gap-6">
                <!-- Image -->
                @foreach($mediafiles as $media)
                <div class="col-span-6 sm:col-span-4 md:col-span-3 2xl:col-span-2">
                    <div class="bg-gray-800 rounded-md px-5 pt-8 pb-5 px-3 sm:px-5 relative hover:shadow-lg">
                        <div class="absolute left-0 top-0 mt-3 ml-3">
                            
                            <div class="relative">
                                <button type="button" aria-hidden="true" data-dropdown-placement="bottom" data-dropdown-toggle="edit-media-{{ $media->id }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                      <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                    </svg>
                                </button>
                                <div class="absolute top-0 left-0 z-40">
                                    <div id="edit-media-{{ $media->id }}" class="hidden bg-dash-300 p-2 rounded-md text-xs" aria-labelledby="Media file editor">
                                        <form style="width: 300px"  action="{{ route('mediafiles.update', $media) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="folder" value="{{ $selectedFolder ? $selectedFolder->slug : $media->folder->slug }}">
                                            <div class="relative">
                                                <span class="text-xs absolute mr-4 right-0 top-2 py-0.5 rounded px-2 bg-dash-200 opacity-0.6">Name</span>
                                                <input class="w-full py-2 pl-4 pr-16 rounded bg-dash-500 focus:ring-4 focus:ring-dash-300 border-none text-sm text-gray-300" placeholder="File Name" type="text" name="name" value="{{ $media->name }}" required>
                                            </div>

                                            <div class="relative">
                                                <span class="text-xs absolute mr-4 right-0 top-4 py-0.5 rounded px-2 bg-dash-200 opacity-0.6">Alt</span>
                                                <input class="w-full py-2 pl-4 pr-16 mt-2 rounded bg-dash-500 focus:ring-4 focus:ring-dash-300 border-none text-sm text-gray-300" placeholder="File Alt" type="text" name="alt" value="{{ $media->alt }}">
                                            </div>

                                            <div class="relative">
                                                <span class="text-xs absolute mr-4 right-0 top-4 py-0.5 rounded px-2 bg-dash-200 opacity-0.6">Title</span>
                                                <input class="w-full py-2 pl-4 pr-16 mt-2 rounded bg-dash-500 focus:ring-4 focus:ring-dash-300 border-none text-sm text-gray-300" placeholder="File Title" type="text" name="title" value="{{ $media->title }}">
                                            </div>

                                            <div class="relative">
                                                <span class="text-xs absolute mr-4 right-0 top-4 py-0.5 rounded px-2 bg-dash-200 opacity-0.6">Caption</span>
                                                <input class="w-full py-2 pl-4 pr-16 mt-2 rounded bg-dash-500 focus:ring-4 focus:ring-dash-300 border-none text-sm text-gray-300" placeholder="File Caption" type="text" name="caption" value="{{ $media->caption }}">
                                            </div>

                                            <button type="submit" class="w-full text-white bg-blue-800/[.8] hover:bg-blue-700/[.8] focus:ring-4 focus:ring-blue-300 font-medium rounded text-xs px-4 py-2 mt-2 focus:outline-none">Update</button>

                                        </form>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                        @if($media->isFileAnImage())
                            <a href="{{ $media->getEncodedImageLink() }}" target="_blank" class="relative flex w-3/5 mx-auto ">
                                <img class="rounded object-cover h-24 mx-auto" alt="{{ $media->alt }}" title="{{ $media->title }}" srcset="{{ $media->getSrcset() }}" sizes="{{ $media->getSizes() }}" src="{{ $media->getEncodedImageLink() }}" width="{{ $media->width }}" height="{{ $media->height }}">
                            </a>
                        @else
                            <a href="{{ $media->getFileLink() }}" target="_blank"  class="relative flex w-3/5 mx-auto ">
                                <img class="rounded object-cover h-24 mx-auto" alt="{{ $media->alt }}" title="{{ $media->title }}" src="{{ asset('vendor/mediafile/imgs/file.svg') }}">
                                <div class="absolute top-0 left-0 right-0 bottom-0 flex items-center justify-center text-white font-medium uppercase">{{ $media->extension }}</div>
                            </a>
                        @endif
                        <div class="font-medium mt-4 text-center truncate">{{ $media->getFileName() }}</div> 
                        <div class="text-gray-400 text-xs text-center mt-0.5">{{ $media->getFileSize() }}</div>
                        <div class="absolute top-0 right-0 mr-2 mt-3 ml-auto">
                            <button id="dropDownFile-{{ $media->id }}-btn" type="button" class="w-5 h-5 block" aria-hidden="true" data-dropdown-placement="bottom-end" data-dropdown-toggle="dropDownFile-{{ $media->id }}"> 
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="more-vertical" data-lucide="more-vertical" class="lucide lucide-more-vertical w-5 h-5 text-slate-500"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg> 
                            </button>
                            <div id="dropDownFile-{{ $media->id }}" class="hidden w-40" aria-labelledby="Image {{ $media->id }} dropdown menu">
                                <ul class="bg-dash-100 p-2 rounded-md text-xs">
                                    <li>
                                        <button type="button" data-parent="dropDownFile-{{ $media->id }}" data-target="{{ $media->isFileAnImage() ? asset($media->getEncodedImageLink()) : asset($media->getFileLink()) }}" class="copy-url-button flex w-full items-center px-2 py-2 rounded-md hover:bg-dash-200"> 
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="lucide lucide-users w-4 h-4 mr-2">
                                              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75" />
                                            </svg> Copy Url 
                                        </button>
                                    </li>
                                    <li>
                                        <form action="{{ route('mediafiles.destroy', $media) }}" method="POST" class="flex w-full">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="folder" value="{{ $selectedFolder ? $selectedFolder->slug : $media->folder->slug }}">
                                            <button type="submit" class="flex w-full items-start px-2 py-2 rounded-md hover:bg-dash-200" onclick="return confirm('Are you sure that you wanna delete this file [{{ $media->getFileName() }}]?');"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" icon-name="trash" data-lucide="trash" class="lucide lucide-trash w-4 h-4 mr-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"></path></svg> Delete </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- pagination -->
    @if($mediafiles)
    {{ $mediafiles->links() }}
     @endif
 </div>
</div>

@push('scripts')
<script>
    const copyUrlBtns = document.querySelectorAll('.copy-url-button');
    copyUrlBtns.forEach(function (copyUrlBtn) {
        copyUrlBtn.addEventListener('click', function (e) {
            e.preventDefault();
            navigator.clipboard.writeText(copyUrlBtn.dataset.target).then(() => {
              console.log('Link copied to clipboard');
              const $targetEl = document.getElementById(copyUrlBtn.dataset.parent);
              const $triggerEl = document.getElementById(copyUrlBtn.dataset.parent + '-btn');
              const dropdown = new Dropdown($targetEl, $triggerEl);
              dropdown.hide();
              /* Resolved - text copied to clipboard successfully */
            },() => {
              console.error('Failed to copy');
              /* Rejected - text failed to copy to the clipboard */
            });
        });
    });
</script>
@endpush
@endsection
