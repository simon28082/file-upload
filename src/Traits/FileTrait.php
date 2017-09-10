<?php

namespace CrCms\Upload\Traits;

use CrCms\Upload\Exceptions\UploadException;
use CrCms\Upload\File;

/**
 * Class FileTrait
 *
 * @package CrCms\Upload\Traits
 */
trait FileTrait
{
    use DirectoryTrait, RenameTrait;

    /**
     * @var File
     */
    protected $file;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $temp;

    /**
     * @var int
     */
    protected $size;

    /**
     * @var string
     */
    protected $extension;

    /**
     * @var int
     */
    protected $error;

    /**
     * @var string
     */
    protected $type;

    /**
     * upload path
     *
     * @var string
     */
    protected $path = './uploads';

    /**
     * @var string
     */
    protected $fullPath;

    /**
     * @return int
     */
    public function getError(): int
    {
        return $this->error;
    }

    /**
     * @param int $error
     * @return FileTrait
     */
    public function setError(int $error): self
    {
        $this->error = $error;
        return $this;
    }

    /**
     * @param string $name
     * @return FileTrait
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $filePath
     * @return $this
     */
    protected function setFile(string $filePath)
    {
        $this->file = new File($filePath);
        return $this;
    }

    /**
     * @return string
     */
    public function getTemp(): string
    {
        return $this->temp;
    }

    /**
     * @param string $temp
     * @return FileTrait
     */
    public function setTemp(string $temp): self
    {
        $this->temp = $temp;
        return $this;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @param int $size
     * @return FileTrait
     */
    public function setSize(int $size): self
    {
        $this->size = $size;
        return $this;
    }

    /**
     * @return string
     */
    public function getExtension(): string
    {
        return $this->extension;
    }

    /**
     * @param string $extension
     * @return FileTrait
     */
    public function setExtension(string $extension): self
    {
        $this->extension = $extension;
        return $this;
    }

    /**
     * @param string $type
     * @return FileTrait
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $path
     * @return FileTrait
     */
    public function setPath(string $path): self
    {
        if (!empty($path)) {
            $this->path = $path;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param array $file
     * @return FileTrait
     */
    protected function setUploadFile(array $file): self
    {
        $this->setName($file['name']);

        $this->setType($file['type']);

        $this->setError($file['error']);

        $this->setSize($file['size']);

        $this->setExtension(pathinfo($this->name, PATHINFO_EXTENSION));

        $this->setTemp($file['tmp_name']);

        $this->setFile($file['tmp_name']);

        $this->setFullPath($file['name']);

        return $this;
    }


    /**
     * @return FileTrait
     */
    public function checkUploadSelf(): self
    {
        if ($this->error !== UPLOAD_ERR_OK) {
            throw new UploadException($this->name, $this->error);
        }

        return $this;
    }

    /**
     * @return FileTrait
     */
    public function checkUploadedFile(): self
    {
        if (!is_uploaded_file($this->temp)) {
            throw new UploadException($this->name, UploadException::IS_NOT_UPLOAD_FILE);
        }

        return $this;
    }

    /**
     * @param string $name
     * @return FileTrait
     */
    public function setFullPath(string $name): self
    {
        $dirs = $this->getHashDir($name);
        $newName = $this->getNewName($name);
        $this->fullPath = $this->path . DIRECTORY_SEPARATOR . $dirs . $newName;

        return $this;
    }

    /**
     * @return string
     */
    public function getFullPath(): string
    {
        return $this->fullPath;
    }

    /**
     * @param File $file
     * @return array
     */
    protected function getUploadInfo(File $file)
    {
        $fileInfo = [];
        $fileInfo['new_name'] = $file->getFilename();
        $fileInfo['hash'] = sha1($file->getRealPath());
        $fileInfo['old_name'] = $this->name;
        $fileInfo['save_path'] = $file->getPathname();
        $fileInfo['full_path'] = $file->getRealPath();
        $fileInfo['full_root'] = str_replace(dirname(getenv('SCRIPT_FILENAME')), '', $file->getRealPath());
        $fileInfo['extension'] = $file->getExtension();
        $fileInfo['mime_type'] = $file->getMime();
        $fileInfo['file_size'] = $file->getSize();
        $fileInfo['complete_time'] = time();
        list($usec, $sec) = explode(" ", microtime());
        $fileInfo['complete_microtime'] = (float)$usec + (float)$sec;
        return $fileInfo;
    }

    /**
     * @return bool
     */
    protected function createDirToFullPath(): bool
    {
        $path = dirname($this->fullPath);
        return is_dir($path) || $this->createDir($path);
    }
}