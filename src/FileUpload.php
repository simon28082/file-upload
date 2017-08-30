<?php

namespace CrCms\Upload;

use CrCms\Upload\Contracts\FileUpload as FileUploadContract;
use Illuminate\Contracts\Config\Repository as Config;

/**
 * Class FileUpload
 *
 * @package CrCms\Upload
 * $this->config()->setNewName(function($oldName){...})->upload()
 */
class FileUpload implements FileUploadContract
{
    /**
     * @var array
     */
    protected $files = [];

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var UploadHandler
     */
    protected $uploadHandler;

    /**
     * FileUpload constructor.
     * @param UploadHandler $uploadHandle
     * @param array $config
     */
    public function __construct(Config $config, UploadHandler $uploadHandle)
    {
        set_time_limit(5 * 60);

        $this->uploadHandler = $uploadHandle;
        $this->config = $config;

        $this->config($this->config->get('upload.default'));
    }

    /**
     * @param array $config
     * @return FileUpload
     */
    public function config(string $uploadType): self
    {
        $allowFunc = [
            'setFileSize',
            'setRename',
            'setCheckMime',
            'setCheckExtension',
            'setExtensions',
            'setHashDirLayer',
            'setPath',
        ];

        foreach ($this->config->get("upload.uploads.{$uploadType}") as $key => $value) {
            if (in_array($key, $allowFunc, true)) {
                call_user_func([$this, $key], $value);
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function upload(): array
    {
        return array_map(function ($file) {
            return method_exists($this, 'handle') ?
                $this->handle($file) : $this->uploadHandler->handle($file);
        }, $this->formatFiles());
    }

    /**
     * format upload files
     * @return array
     */
    protected function formatFiles(): array
    {
        $files = [];
        if (!empty($_FILES)) {
            $temp = [];
            foreach ($_FILES as $key => $values) {
                if (is_array($values['name'])) {
                    foreach ($values['name'] as $k => $vo) {
                        if (empty($vo)) continue;
                        $temp['name'] = $vo;
                        $temp['type'] = $values['type'][$k];
                        $temp['tmp_name'] = $values['tmp_name'][$k];
                        $temp['error'] = $values['error'][$k];
                        $temp['size'] = $values['size'][$k];
                        $temp['__name'] = $key;
                        $files[] = $temp;
                    }
                } else {
                    if (empty($values['name'])) continue;
                    $values['__name'] = $key;
                    $files[] = $values;
                }
            }
        }

        return $files;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return $this|mixed
     */
    public function __call(string $name, array $arguments)
    {
        if (method_exists($this->uploadHandler, $name)) {
            $result = call_user_func_array([$this->uploadHandler, $name], $arguments);
            if ($result instanceof $this->uploadHandler) {
                return $this;
            }
            return $result;
        }

        throw new \BadMethodCallException("method [{$name}] is not exists");
    }
}