<?php

namespace CrCms\Upload;

use CrCms\Upload\Contracts\Resolver;
use Illuminate\Contracts\Config\Repository as Config;
use BadMethodCallException;
use CrCms\Upload\Contracts\Uploader as UploaderContract;

/**
 * Class FileUpload
 *
 * @package CrCms\Upload
 */
class Uploader
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var UploaderContract
     */
    protected $uploader;

    /**
     * @var Resolver
     */
    protected $resolver;

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

//        $this->setUploader($collection['uploader'], $collection);
//        $this->setResolver($collection['resolver']);

        return $this;
    }

    /**
     * @param array $files
     * @return array
     */
    public function upload(array $files): array
    {
        return array_map([$this, 'uploadFile'], $this->resolver->resolve($files));
    }

    /**
     * @param string $uploader
     * @param array $config
     * @return Uploader
     */
    public function setUploader(string $uploader, array $config): self
    {
        $this->uploader = Factory::upload($uploader, $config);
        return $this;
    }

    /**
     * @param string $resolver
     * @return Uploader
     */
    public function setResolver(string $resolver): self
    {
        $this->resolver = Factory::resolve($resolver);
        return $this;
    }

    /**
     * @param string $collection
     * @return array
     */
    protected function uploaderConfig(string $collection): array
    {
        $collection = $this->config->get("upload.collections.{$collection}");
        $defaultCollection = $this->config->get("upload.drivers.{$collection['driver']}");
        return array_merge($defaultCollection, $collection);
    }

    /**
     * @param array $file
     * @return array
     */
    protected function uploadFile(array $file): array
    {
        $uploader = Factory::upload($this->configCollection['uploader'], $file, $this->configCollection);
        return $uploader->upload();
        //return $this->uploader->upload($file);
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return $this|mixed
     */
    public function __call(string $name, array $arguments)
    {
        if (method_exists($this->uploader, $name)) {
            $result = call_user_func_array([$this->uploader, $name], $arguments);
            if ($result instanceof $this->uploader) {
                return $this;
            }
            return $result;
        }

        throw new BadMethodCallException("method [{$name}] is not exists");
    }
}