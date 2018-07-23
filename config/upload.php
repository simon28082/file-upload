<?php

return [

    'default' => 'default',

    'collections' => [

        'default' => [
            'driver' => 'default',
            'options' => [
                'setFileSize' => 1024 * 1024 * 2,
                'setRename' => true,
                'setCheckMime' => true,
                'setCheckExtension' => true,
                'setExtensions' => ['jpg', 'jpeg', 'gif', 'png'],
                'setHashDirLayer' => 2,
                'setPath' => './uploads',
            ]
        ],
    ],

    'drivers' => [
        'default' => [
            'uploader' => 'default',
            'resolver' => 'default',
            'options' => [
                'size' => 1024 * 1024 * 2,
                'check_mime' => true,
                'mimes' => ['text/plain'],
                'check_extension' => true,
                'extensions' => ['jpg', 'jpeg', 'gif', 'png'],
                'rename' => true,
                'directory_layer' => 2,
                'path' => './uploads',
            ]
        ],
        'webupload' => [
            'chunk_name' => 'chunk',
            'chunks_name' => 'chunks',
            'size_name' => 'orig_size',
            'new_name' => 'new_name',
        ],
    ],

    /*'resolvers' => [
        'default' =>
    ],*/

    'timeout' => 5 * 60,
];