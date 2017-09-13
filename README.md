# File Upload

## Config

```
[

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
        'test' => [
            'driver' => \CrCms\Upload\Drives\WebUpload::class,//
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
        'webupload' => [
            'chunk_name' => 'chunk',
            'chunks_name' => 'chunks',
            'size_name' => 'orig_size',
            'new_name' => 'new_name',
        ],
    ],

]

```

## Example

### DefaultUpload

```
require '../vendor/autoload.php';

$config = require '../config/upload.php';
$config = new \Illuminate\Config\Repository(['upload'=>$config]);

$upload = new \CrCms\Upload\FileUpload($config);

$file = $upload->config('test')->upload();
//default
$upload->upload();

print_r($file);
```

## Install

You can install the package via composer:

```
composer require crcms/file-upload
```

## Laravel

Modify ``config / app.php``

```
'providers' => [
    CrCms\Upload\LaravelServiceProvider::class,
]

```

If you'd like to make configuration changes in the configuration file you can pubish it with the following Aritsan command:
```
php artisan vendor:publish --provider="CrCms\Upload\LaravelServiceProvider"
```

## Laravel Testing

View ``test/upload.php``

## License
MIT
