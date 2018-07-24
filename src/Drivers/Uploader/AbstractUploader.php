<?php

namespace CrCms\Upload\Drivers\Uploader;

use CrCms\Upload\Components\DirectoryLayerComponent;
use CrCms\Upload\Components\ExtensionComponent;
use CrCms\Upload\Components\MimeComponent;
use CrCms\Upload\Components\PathComponent;
use CrCms\Upload\Components\RenameComponent;
use CrCms\Upload\Components\SizeComponent;
use CrCms\Upload\File;
use CrCms\Upload\Exceptions\SizeException;
use CrCms\Upload\Exceptions\TypeErrorException;
use CrCms\Upload\Exceptions\UploadException;

/**
 * Class AbstractUploader
 * @package CrCms\Upload\Drivers\Uploader
 */
abstract class AbstractUploader
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var File
     */
    protected $uploadedFile;

    /**
     * @var File
     */
    protected $uploadFile;

    /**
     * @var array
     */
    protected $uploadInfo;

    /**
     * @var SizeComponent
     */
    protected $sizeComponent;

    /**
     * @var ExtensionComponent
     */
    protected $extensionComponent;

    /**
     * @var RenameComponent
     */
    protected $renameComponent;

    /**
     * @var MimeComponent
     */
    protected $mimeComponent;

    /**
     * @var DirectoryLayerComponent
     */
    protected $directoryComponent;

    /**
     * @var PathComponent
     */
    protected $pathComponent;

    /**
     * @var int
     */
    protected $completeSecondTime;

    /**
     * @var float
     */
    protected $completeMicroTime;

    /**
     * AbstractUploader constructor.
     * @param array $uploadInfo
     * @param array $config
     */
    public function __construct(array $uploadInfo, array $config)
    {
        $this->config = $config;
        $this->uploadInfo = $uploadInfo;

        $this->uploadFile = new File($this->uploadInfo['tmp_name']);

        $this->sizeComponent = new SizeComponent($this->config['size']);
        $this->extensionComponent = new ExtensionComponent($this->config['extensions'], $this->config['check_extension']);
        $this->mimeComponent = new MimeComponent($this->config['mimes'], $this->config['check_mime']);
        $this->renameComponent = new RenameComponent($this->config['rename'], $this->uploadInfo['name']);
        $this->directoryComponent = new DirectoryLayerComponent($this->uploadInfo['name'], $this->config['directory_layer']);
        $this->pathComponent = new PathComponent($this->config['path'], $this->renameComponent, $this->directoryComponent);
    }

    /**
     * @return array
     */
    protected function allUploadInfo(): array
    {
        return [
            'file' => $this->uploadedFile,
            'info' => $this->uploadInfo,
            'second_time' => $this->completeSecondTime,
            'micro_time' => $this->completeMicroTime,
        ];
    }

    /**
     * @return void
     */
    protected function uploadedTime(): void
    {
        $this->completeSecondTime = time();
        list($usec, $sec) = explode(" ", microtime());
        $this->completeMicroTime = (float)$usec + (float)$sec;
    }

    /**
     * @return array
     */
    public function getUploadInfo(): array
    {
        return $this->uploadInfo;
    }

    /**
     * @return File
     */
    public function getUploadedFile(): File
    {
        return $this->uploadedFile;
    }

    /**
     * @return AbstractUploader
     */
    public function checkUploadedFile(): self
    {
        if (!is_uploaded_file($this->uploadInfo['tmp_name'])) {
            throw new UploadException($this->uploadInfo['name'], UploadException::IS_NOT_UPLOAD_FILE);
        }

        return $this;
    }

    /**
     * @return AbstractUploader
     */
    public function checkUploadSelf(): self
    {
        if ($this->uploadInfo['error'] !== UPLOAD_ERR_OK) {
            throw new UploadException($this->uploadInfo['name'], $this->uploadFile['error']);
        }

        return $this;
    }

    /**
     * @return AbstractUploader
     */
    public function checkSize(): self
    {
        if (!$this->sizeComponent->checkSize($this->uploadInfo['size'])) {
            throw new SizeException($this->uploadInfo['name']);
        }

        return $this;
    }

    /**
     * @return AbstractUploader
     */
    public function checkMime(): self
    {
        if (!$this->mimeComponent->checkMime($this->uploadFile->getMime())) {
            throw new TypeErrorException($this->uploadInfo['name'], 'mime');

        }

        return $this;
    }

    /**
     * @return AbstractUploader
     */
    public function checkExtension(): self
    {
        if (!$this->extensionComponent->checkExtension(pathinfo($this->uploadInfo['name'], PATHINFO_EXTENSION))) {
            throw new TypeErrorException($this->uploadInfo['name'], 'extension');
        }

        return $this;
    }
}