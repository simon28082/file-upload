<?php

namespace CrCms\Upload;

use CrCms\Upload\Drivers\DefaultUpload;
use CrCms\Upload\Contracts\FileUpload as FileUploadContract;
use CrCms\Upload\Drivers\WebUpload;
use DomainException;

/**
 * Class FileUploadFactory
 * @package CrCms\Upload
 */
class FileUploadFactory
{

    public static function factory(string $driver,array $config): FileUploadContract
    {
        switch ($driver) {
            case 'default':
                return new DefaultUpload($config);
            case 'webupload':
                return new WebUpload($config);
        }

        throw new DomainException("The driver [$driver] not exists");
    }
}