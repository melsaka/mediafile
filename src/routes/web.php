<?php
use Melsaka\MediaFile\Controllers\GetController;

$folderController = GetController::ifPublished('FolderController');
$mediaController =  GetController::ifPublished('MediaController');

Route::resource('mediafiles-folders', $folderController)->only(['store', 'update', 'destroy']);
Route::resource('mediafiles', $mediaController)->only(['index', 'store', 'update', 'destroy']);
