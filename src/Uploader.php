<?php

namespace CrCms\Upload;

use Illuminate\Contracts\Config\Repository as Config;

/**
 * Class Uploader
 * @package CrCms\Upload
 */
class Uploader
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var array
     */
    protected $configCollection;

    /**
     * FileUpload constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->config($this->config->get('upload.default'));
    }

    /**
     * @param string $collection
     * @return Uploader
     */
    public function config(string $collection): self
    {
        $this->configCollection = $this->uploaderConfig($collection);

        return $this;
    }

    /**
     * @param array $files
     * @return array
     */
    public function upload(array $files): array
    {
        return array_map([$this, 'uploadFile'], Factory::resolve($this->configCollection['resolver'])->resolve($files));
    }

    /**
     * @param string $collection
     * @return array
     */
    protected function uploaderConfig(string $collection): array
    {
        $collection = $this->config->get("upload.collections.{$collection}");
        $defaultCollection = $this->config->get("upload.drivers.{$collection['driver']}");

        $options = isset($collection['options']) ?
            array_merge($defaultCollection['options'], $collection['options']) :
            $defaultCollection['options'];

        $collection = array_merge($defaultCollection, $collection);
        $collection['options'] = $options;

        return $collection;
    }

    /**
     * @param array $file
     * @return array
     */
    protected function uploadFile(array $file): array
    {
        return Factory::upload($this->configCollection['uploader'], $file, $this->configCollection['options'])->upload();
    }
}