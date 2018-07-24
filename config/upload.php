<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default upload collection
    |--------------------------------------------------------------------------
    |
    | Select one of the keys in the collections below as the default upload configuration
    |
    */

    'default' => 'default',

    /*
    |--------------------------------------------------------------------------
    | All upload collection config
    |--------------------------------------------------------------------------
    |
    | All available upload configuration options are included in this collection.
    | The child elements in the collection will select a default execution driver in the driver.
    | If the 'options' option does not exist, the 'options' option in the driver will be used.
    |
    */

    'collections' => [

        'default' => [
            'driver' => 'default'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | All upload drivers
    |--------------------------------------------------------------------------
    |
    | Default configuration for all upload and parsing drivers
    |
    */
    'drivers' => [
        'default' => [
            'uploader' => 'default',
            'resolver' => 'default',
            'options' => [
                'size' => 1024 * 1024 * 2,
                'check_mime' => true,
                'mimes' => ['image/gif', 'image/jpeg', 'image/ktx', 'image/png'],
                'check_extension' => true,
                'extensions' => ['jpg', 'jpeg', 'gif', 'png'],
                'rename' => true,
                'directory_layer' => 2,
                'path' => './uploads',
            ]
        ],
        /*'webupload' => [
            'chunk_name' => 'chunk',
            'chunks_name' => 'chunks',
            'size_name' => 'orig_size',
            'new_name' => 'new_name',
        ],*/
    ],

    /*'resolvers' => [
        'default' =>
    ],*/

    'timeout' => 5 * 60,
];