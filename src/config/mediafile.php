<?php

return [
    // Dsiable Or Enable built in routes [Enabled by Default].
    'routes' => true,
    
    // The main folder name in /storage/app/public/[folder_name] the default is 'media'.
    // Set it to [null or false] if you don't want your folders to be in one main folder
    'folder_name'   => null,

    // Every uploaded image is gonna have an encoded version of it, and also encoded 
    // thumbnails with different sizes, the default encoded image data is 'webp'.
    // For supported formats check this link: https://image.intervention.io/v2/api/encode
    'image_encoder' => 'webp',

    // This creates 5 different thumbnails of the image file.
    'image_thumbnail_sizes' => [
        'h'     =>  1600,
        'l'     =>  1200,
        'm'     =>  900,
        's'     =>  600,
        'xs'    =>  300,
    ],

    // For example, if we uploaded [image.png] the thumbnail name gonna be: path/to/folder/image_300.webp. you must not choose numbers as separator.
    'image_thumbnail_separator' => '_',
];
