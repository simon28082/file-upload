<?php

return [

    'default' => 'default',

    'uploads' => [

        'default' => [
            'driver' => \CrCms\Upload\Drives\DefaultUpload::class,//
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

    'drives' => [
        'plupload' => [
            'chunk_name' => 'chunk',
            'chunks_name' => 'chunks',
            'old_name' => 'old_name',
            //'size_name' => 'orig_size',
            'new_name' => 'new_name',
        ],
    ],

];