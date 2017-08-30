<?php

namespace CrCms\Upload;

use Illuminate\Support\ServiceProvider;

/**
 * Class LaravelServiceProvider
 *
 * @package CrCms\Upload
 * @author simon
 */
class LaravelServiceProvider extends ServiceProvider
{
    /**
     * @var string
     */
    protected $namespaceName = 'upload';

    /**
     * @var string
     */
    protected $packagePath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;

    /**
     *
     */
    public function boot()
    {
        $this->publishes([
            $this->packagePath . 'config' => config_path('upload.php'),
        ],static::class);
    }

    /**
     *
     */
    public function register()
    {
        $this->mergeConfigFrom(
            $this->packagePath . "config/{$this->namespaceName}.php", $this->namespaceName
        );
    }
}