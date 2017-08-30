<?php

namespace CrCms\Upload;

/**
 * Class FileUpload
 *
 * @package CrCms\Upload
 * $this->config()->setNewName(function($oldName){...})->upload()
 */
class FileUpload implements \CrCms\Upload\Contracts\FileUpload
{
    /**
     * @var array
     */
    protected $files = [];

    /**
     * @var UploadHandler
     */
    protected $uploadHandler = null;

    /**
     * FileUpload constructor.
     * @param UploadHandler $uploadHandle
     * @param array $config
     */
    public function __construct(UploadHandler $uploadHandle, array $config = [])
    {
        set_time_limit(5 * 60);

        $this->uploadHandler = $uploadHandle;

        if ($config) $this->config($config);
    }

    /**
     * @param array $config
     * @return FileUpload
     */
    public function config(array $config): self
    {
        $allowFunc = [
            'file_size' => 'setFileSize',
            'rename' => 'setRename',
            'check_mime' => 'setCheckMime',
            //'mimes' => 'setMimes',
            'check_extension' => 'setCheckExtension',
            'extensions' => 'setExtensions',
            'hash_dir_layer' => 'setHashDirLayer',
            'path' => 'setPath',
        ];

        foreach ($config as $key => $value) {
            if (in_array($key, array_keys($allowFunc), true)) {
                call_user_func([$this, $allowFunc[$key]], $value);
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