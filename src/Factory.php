<?php

namespace CrCms\Upload;

use CrCms\Upload\Contracts\Resolver;
use CrCms\Upload\Drivers\Resolver\DefaultResolver;
use CrCms\Upload\Contracts\Uploader;
use CrCms\Upload\Drivers\Uploader\DefaultUploader;
use CrCms\Upload\Drivers\Uploader\WebUploader;
use DomainException;

/**
 * Class Factory
 * @package CrCms\Upload
 */
class Factory
{
    /**
     * @param string $driver
     * @param array $config
     * @return Uploader
     */
    public static function upload(string $driver, array $uploadInfo, array $config): Uploader
    {
        switch ($driver) {
            case 'default':
                return new DefaultUploader($uploadInfo, $config);
            case 'webupload':
                /*return new WebUploader($config);*/
        }

        if ((bool)$result = static::autoDriver($driver)) {
            return $result;
        }

        throw new DomainException("The upload driver [$driver] not exists");
    }

    /**
     * @param string $driver
     * @return Resolver
     */
    public static function resolve(string $driver): Resolver
    {
        switch ($driver) {
            case 'default':
                return new DefaultResolver;

        }

        if ((bool)$result = static::autoDriver($driver)) {
            return $result;
        }

        throw new DomainException("The resolve driver [$driver] not exists");
    }

    /**
     * @param string $driver
     * @return mixed
     */
    protected static function autoDriver(string $driver)
    {
        if (class_exists($driver)) {
            $instance = new $driver;
            if ($instance instanceof Resolver) {
                return $instance;
            }
        }

        return false;
    }
}