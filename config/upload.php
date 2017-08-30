<?php
return [

    'default' => 'default',

    'uploads' => [

        'default' => [
            'setFileSize' => 20000,
            'setRename' => true,
            'setCheckMime' => true,
            'setCheckExtension' => true,
            'setExtensions' => ['jpg', 'jpeg', 'gif', 'png'],
            'setHashDirLayer' => 2,
            'setPath' => './uploads',
        ],
        
    ],

    'plupload' => [
        'chunk_name' => 'chunk',
        'chunks_name' => 'chunks',
        'old_name' => 'old_name'
    ],
];