# MediaFile

Upload files and images to your laravel app with less effort.

## Features

- Supports the ability to `create`, `edit`, `delete` folders in `'storage/app/public/'`.

- Upload files and images into these folders in a super easy way.

- Creating thumbnails automatically with width of your choice for every uploaded image.

- Encodes the thumbnail images in given format ([Available formats](https://image.intervention.io/v2/api/encode)) default: `'webp'`.

- Besides the ability to rename files, add image alt, title and caption. You can fetch file size and get image srcset and sizes in an easy way `$model->getSrcset()`. 

- Offers built-in routes, controllers, models, and views to get you started right away after installation without writing any additional code.

## Example Usage

Upload a file to the server, and place it into the images directory in `'storage/app/public/media/images'`. This will create and returns a Media record that can be used to refer to the file.

```php
// $request->folder = 'images'.
// get the images folder that you wanna store the file into it.
$folder = Folder::whereSlug($request->folder)->firstOrFail();

// init MediaFile library
$mediafile = new MediaFile($folder);

// Saves the file in [/storage/app/public/media/[folder_slug]/], 
// store it in database and returns Media eloquent model instance.
$media = $mediafile->store($request->file('file')); 
```

You can also update a media record by simply do so:

```php
$mediafile = new MediaFile($folder);

$media = $mediafile->update($request, $media);
```

And delete media record by simply:

```php
$mediafile = new MediaFile($folder);

$mediafile->delete($media);
```

## Installation

Add the package to your Laravel app using composer

```php
composer require melsaka/mediafile
```

Register the package's service provider in config/app.php. In Laravel versions 5.5 and beyond, this step can be skipped if package auto-discovery is enabled.

```php
'providers' => [
    ...
    Melsaka\MediaFile\MediaFileServiceProvider::class,
    ...
];
```

Publish the config file (src/config/mediafile.php) of the package using artisan.

```php
php artisan vendor:publish --tag=mediafile-config
```

Publish the assets files (src/public/) of the package using artisan.

```php
php artisan vendor:publish --tag=mediafile-assets
```

Run the migrations to add the required tables to your database.

```php
php artisan migrate
```

**(Optional)** You can also publish controllers, migrations and views using these artisan commands.

```php
// publish controllers
php artisan vendor:publish --tag=mediafile-controllers

// publish migrations
php artisan vendor:publish --tag=mediafile-migrations

// publish views
php artisan vendor:publish --tag=mediafile-views
```

## Documentation

Coming soon.

## License

This package is released under the MIT license (MIT).
