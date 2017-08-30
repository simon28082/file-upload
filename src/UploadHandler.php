<?php

namespace CrCms\Upload;

use CrCms\Upload\Exceptions\SizeException;
use CrCms\Upload\Exceptions\TypeErrorException;
use CrCms\Upload\Exceptions\UploadException;
use CrCms\Upload\Traits\DirectoryTrait;
use CrCms\Upload\Traits\ExtensionTrait;
use CrCms\Upload\Traits\MimeTrait;
use CrCms\Upload\Traits\RenameTrait;
use CrCms\Upload\Traits\SizeTrait;
use CrCms\Upload\Contracts\FileUpload;

/**
 * Class UploadHandle
 * @package Simon\Upload
 */
class UploadHandler
{
    use MimeTrait, SizeTrait, ExtensionTrait, DirectoryTrait, RenameTrait;

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
     * new file path
     * @var string
     */
    protected $path = './uploads';

    /**
     * @var null|FileUpload
     */
//    protected $fileUpload = null;
//
//    /**
//     * UploadHandler constructor.
//     * @param FileUpload $fileUpload
//     */
//    public function __construct(FileUpload $fileUpload)
//    {
//        $this->fileUpload = $fileUpload;
//    }

    /**
     * @return int
     */
    public function getError(): int
    {
        return $this->error;
    }

    /**
     * @param int $error
     * @return UploadHandler
     */
    public function setError(int $error): self
    {
        $this->error = $error;
        return $this;
    }

    /**
     * @param string $name
     * @return UploadHandler
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
     */
    protected function setFile(string $filePath)
    {
        $this->file = new File($filePath);
        return;
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
     */
    public function setExtension(string $extension): self
    {
        $this->extension = $extension;
        return $this;
    }

    /**
     * @param string $type
     * @return UploadHandler
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
     * @return UploadHandler
     */
    public function setPath(string $path): self
    {
        if (!empty($path)) {
            $this->path = $path;
        }

        $dirs = $this->getHashDir($this->name);
        $newName = $this->getNewName() ?: $this->getDefaultNewName($this->name);
        $this->path = $this->path . DIRECTORY_SEPARATOR . $dirs . $newName;

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
     * @param array $fileInfo
     * @return UploadHandler
     */
    public function setUploadFile(array $fileInfo): self
    {
        array_walk($fileInfo, function ($value, $key) {
            if (in_array($key, ['name', 'size', 'type', 'error'], true)) {
                //$this->{$key} = $value;
                call_user_func([$this, 'set' . ucwords($key)], $value);
            }
        });

        $this->setExtension(pathinfo($this->name, PATHINFO_EXTENSION));

        $this->setTemp($fileInfo['tmp_name']);

        $this->setFile($fileInfo['tmp_name']);

        return $this;
    }

    /**
     * @param array $fileInfo
     * @param string $path
     * @return array
     */
    public function handle(array $fileInfo, string $path = ''): array
    {
        $this->setUploadFile($fileInfo);//or
        //$this->setName()->setTemp()->setSize()->setError()->setExtension();

        $this->checkUploadFile();//or
        //$this->checkFileExtension()->checkFileMime()->checkFileSize()->checkUploadedFile()->checkUploadSelf()

        $this->upload($path);//or
        //$this->setPath($path)->moveUploadFile();

        return $this->getUploadInfo();
    }

    /**
     * @return UploadHandler
     */
    public function moveUploadFile(): self
    {
        $path = dirname($this->path);
        is_dir($path) || $this->createDir($path);

        if (!move_uploaded_file($this->temp, $this->path)) {
            throw new UploadException($this->name, UploadException::MOVE_TMP_FILE_ERR);
        }

        $this->setFile($this->path);

        return $this;
    }

    /**
     * @param string $path
     * @return UploadHandler
     */
    public function upload(string $path = ''): self
    {
        $this->setPath($path);

        $this->moveUploadFile();

        return $this;
    }

    /**
     * @return array
     */
    public function getUploadInfo()
    {
        $file = [];
        $file['new_name'] = $this->file->getBasename();
        $file['hash'] = sha1($this->file->getRealPath());
        $file['old_name'] = $this->name;
        $file['save_path'] = $this->file->getPath();
        $file['full_path'] = $this->file->getRealPath();
        $file['full_root'] = str_replace(dirname(getenv('SCRIPT_FILENAME')), '', $this->file->getRealPath());
        $file['extension'] = $this->file->getExtension();
        $file['mime_type'] = $this->file->getFileMime();
        $file['file_size'] = $this->file->getSize();
        $file['complete_time'] = time();
        list($usec, $sec) = explode(" ", microtime());
        $file['complete_microtime'] = (float)$usec + (float)$sec;

        return $file;
    }

    /**
     * @return UploadHandler
     */
    public function checkUploadFile(): self
    {
        $this->checkUploadedFile();

        $this->checkUploadSelf();

        $this->checkFileSize();

        $this->checkFileExtension();

        $this->checkFileMime();

        return $this;
    }

    /**
     * @return UploadHandler
     */
    public function checkFileMime(): self
    {
        if ($this->getCheckMime()) {
            $mime = $this->getExtensionMime($this->extension);
            if ($mime !== $this->file->getFileMime()) {
                throw new TypeErrorException($this->name, 'mime');
            }
        }

        return $this;
    }

    /**
     * @return UploadHandler
     */
    public function checkFileExtension(): self
    {
        if ($this->getCheckExtension() && !in_array(strtolower($this->extension), $this->getExtensions(), true)) {
            throw new TypeErrorException($this->name, 'extension');
        }

        return $this;
    }

    /**
     * @return UploadHandler
     */
    public function checkUploadSelf(): self
    {
        if ($this->error !== UPLOAD_ERR_OK) {
            throw new UploadException($this->name, $this->error);
        }

        return $this;
    }

    /**
     * @return UploadHandler
     */
    public function checkFileSize(): self
    {
        //dd($this->temp,$this->name,$this->type,$this->error,$this->extension,$this->size,$this->path);
        if ($this->getFileSize() < $this->file->getSize()) {
            throw new SizeException($this->name);
        }

        return $this;
    }

    /**
     * @return UploadHandler
     */
    public function checkUploadedFile(): self
    {
        if (!is_uploaded_file($this->temp)) {
            throw new UploadException($this->name, UploadException::IS_NOT_UPLOAD_FILE);
        }

        return $this;
    }
}