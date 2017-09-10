<?php

namespace CrCms\Upload\Drives;

use CrCms\Upload\Contracts\FileUpload;
use CrCms\Upload\Exceptions\UploadException;
use CrCms\Upload\File;
use CrCms\Upload\Traits\ExtensionTrait;
use CrCms\Upload\Traits\FileTrait;
use CrCms\Upload\Traits\MimeTrait;
use CrCms\Upload\Traits\SizeTrait;

/**
 * Class DefaultUpload
 *
 * @package CrCms\Upload\Drives
 */
class DefaultUpload implements FileUpload
{
    use FileTrait, ExtensionTrait, MimeTrait, SizeTrait;

    /**
     * @param array $file
     * @return array
     */
    public function upload(array $file): array
    {
        $this->setUploadFile($file);

        $this->checkUploadFile();

        return $this->getUploadInfo(
            $this->moveUploadFile()
        );
    }

    /**
     * @return File
     */
    protected function moveUploadFile(): File
    {
        $this->createDirToFullPath();

        if (!move_uploaded_file($this->getTemp(), $this->fullPath)) {
            throw new UploadException($this->getName(), UploadException::MOVE_TMP_FILE_ERR);
        }


        return new File($this->fullPath);
    }

    /**
     * @return void
     */
    protected function checkUploadFile()
    {
        $this->checkUploadedFile();

        $this->checkUploadSelf();

        $this->checkSize($this->size);

        $this->checkExtension($this->extension);

        $this->checkMime($this->file->getMime());
    }
}