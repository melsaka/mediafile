# MediaFile

Simplify the process of adding files and images to your Laravel app.


## Features

- Supports the ability to `create`, `edit`, `delete` folders in `'storage/app/public/'`.

- Upload files and images into these folders in a super easy way.

- Creating thumbnails automatically with width of your choice for every uploaded image.

- Encodes the thumbnail images in given format ([Available formats](https://image.intervention.io/v2/api/encode)) default: `'webp'`.

- Besides the ability to rename files, add image alt, title and caption. You can fetch file size and get image srcset and sizes in an easy way `$media->getSrcset()`. 

- Offers built-in routes, controllers, models, and views to get you started right away after installation without writing any additional code.


## Example Usage

Upload a file to the server, and place it into the images directory in `'storage/app/public/media/images'`. 

This will create and returns a `Media` record that can be used to refer to the file.

```php
$folder = Folder::whereSlug($request->folder)->firstOrFail();
```

```php
$mediafile = new MediaFile($folder);

$media = $mediafile->store($request->file('file')); 
```

You can also update a media record by simply do so:

```php
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

Run the app with the `php artisan serve` command and go to `localhost:8000/mediafiles`.

Over there you gonna find a complete setup that allows you to manage folders, files and images.

**(Optional)** You can also publish controllers, migrations and views using these artisan commands.

```php
// publish controllers
php artisan vendor:publish --tag=mediafile-controllers

// publish migrations
php artisan vendor:publish --tag=mediafile-migrations

// publish views
php artisan vendor:publish --tag=mediafile-views
```


## How To Use

Before you upload images you must create a folder to put images into it, folders are created into `storage/app/public/media/` folder by default,

but you can customize this to create them into `storage/app/public/` folder from the `config/mediafile.php` file.

We can use the `MediaFolder` class to easily create folders like this:

```php
// import the MediaFolder Class
use Melsaka\MediaFile\MediaFolder;

// create a MediaFolder instance
$mediafolder = new MediaFolder();

// and finally create the folder 
$mediafolder->store($request->name, $request->order);
```

The store method accpets 2 params the `$name` of the folder and the `$order` in numbers. it creates unique slug automatically based on the `$name` value.

To edit a folder you must have in your $request 3 inputs (`name`, `slug`, `order`) all of them are `required`. 

***Note:*** Both methods return an instance of `Folder` eloquent instance.

```php
$mediafolder->update($request, $folder);
```

It's so easy to delete a folder and all it's files.

```php
$mediafolder->delete($folder);
```

After creating folders you can upload files/images into them using the `MediaFile` class.

```php
// import the MediaFile Class
use Melsaka\MediaFile\MediaFile;

// select the folder that you wanna upload images into it.
$folder = Folder::whereSlug($request->folder)->firstOrFail();

// create a MediaFile instance
$mediafile = new MediaFile($folder);

// and finally upload/store the iamge 
$mediafile->store($request->file('image'));
```

***Note:*** To upload files, you must be logged in, becuase the `MediaFile` class assigns `auth()->id()` to the `user_id` column in database, 

but for testing purposes you can do so: `new MediaFile($folder, false)` to upload files without being logged in, and in this case it will assign `0` to the `user_id` column in database.

When you try to update an image (`name`, `alt`, `title`, `caption`) only the `name` is required.

The `store` and `update` methods returns an instance of `Media` eloquent instance, and you can update files like this:

```php
$mediafile->update($request, $media);
```

It's so easy to delete a file as well.

```php
$mediafile->delete($media);
```

You can also check if a file belongs to a folder like this:

```php
$mediafile = new MediaFile($folder);

$mediafile->isFolder($media);
```

Here's what you can get from `Media` eloquent instance.

```php
use Melsaka\MediaFile\Models\Media;

$media = Media::first();

// User Instance
$media->user;

// Fodler Instance
$media->folder;

// File name without extension
$media->name;

// File extension
$media->extension;

// The encoder used for this image, ex: webp
$media->encoder;

// The type of the file, ex: Image/png
$media->type;

// The uri of the file, ex: storage/images/2023/08/image.png
$media->uri;

// The width of the image, ex: 1920
$media->width;

// The height of the image, ex: 1080
$media->height;

// The alt of the image file
$media->alt;

// The title of the image file
$media->title;

// The caption of the image file
$media->caption;
```

`Media` eloquent instance has also some useful helpers (methods) for files/images, that you may need to use.

```php
// returns the original file url
$media->getFileLink();

// returns the full file name, ex: image.png
$media->getFileName();

// returns file size, ex: 128 kb
$media->getFileSize();

// returns true if file is image
$media->isFileAnImage();

// returns the url of the encoded version of the original image file.
$media->getEncodedImageLink();

// returns the sizes and urls of the image thumbnails when $onlySizes is true, 
// when you set $onlySizes to false it will returns the seperator, sizes, and urls. 
// The $name param allows to get the url of a single thumbnail, ex: $media->getThumbnail('s')
$media->getThumbnails($name = null, $onlySizes = true);

// returns the srcset of an image file.
$media->getSrcset();

// returns the sizes of an image file.
$media->getSizes();
```


## MediaFile Configurations

Now let's take a look at the `config/mediafile.php` file and what are the options available there.


### Built-in Routes and Controllers

`MediaFile` offers a ready to go setup of routes and controllers that allows you to upload files and images without writing a single line of code.

This feature is enabled by deafult, and you can access it by visiting `/mediafiles` in your app. for example: `example.test/mediafiles`.

To disable this feature, change the `'routes'` value to `false` in the `config/mediafile.php` file, `'routes' => false`.


### The Media Folder

`MediaFile` allows you to create folders before you upload files into them, and by default the main folder called `media` and folders are being created in this path `storage/app/public/media/`.

You can change the name of the main folder `media` by changing the value of `'folder_name'` in the `config/mediafile.php` file, 

and if you want to create folders inside the public folder: `storage/app/public/` directly wihtout a main folder you can do so by changing the value to null like his: `'folder_name' => null`.


### Image Encoder

Every uploaded image is gonna have an encoded version of it, and also encoded thumbnails with different sizes, the default encoded image data is `webp`. 

But you can change it to whatever image format you perfer by changing the `image_encoder` value in the `config/mediafile.php` file.

To learn more about the supported formats check this link: https://image.intervention.io/v2/api/encode.


### Image Thumbnails

Every uploaded image is gonna have 5 different encoded thumbnails with different sizes (`1600w`, `1200w`, `900w`, `600w`, `300w`).

You can customize the number of thumbnails and the width of each thumbnail in the `config/mediafile.php` file, by changing the value of `image_thumbnail_sizes`.


### Image Thumbnail Separator

The default value of `image_thumbnail_separator` in the `config/mediafile.php` file is: `_`, 

and it represent how you want to seperate between the name of the image and the size of the image thumbnail, 

which means, If we upload an image named `cat.png` then the thumbnails of this image are gonna be named this way: 

(`cat_300.webp`, `cat_600.webp`, `cat_900.webp`, etc..) 


## License

This package is released under the MIT license (MIT).