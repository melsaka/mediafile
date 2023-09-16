<?php

namespace Melsaka\MediaFile\Controllers;

class GetController
{
    public static function isPublished($controller)
    {
        return file_exists(app_path('Http/Controllers/MediaFile/'.$controller.'.php'));
    }

    public static function ifPublished($controller)
    {
        return GetController::isPublished($controller) ? '\\App\\Http\\Controllers\\MediaFile\\'. $controller : '\\Melsaka\\MediaFile\\Controllers\\'. $controller;
    }
}
