<?php

namespace CrCms\Upload\Drivers\Uploader;

use CrCms\Upload\Contracts\Uploader;
use CrCms\Upload\Exceptions\UploadException;
use CrCms\Upload\File;

/**
 * Class DefaultUploader
 * @package CrCms\Upload\Drivers\Uploader
 */
class DefaultUploader extends AbstractUploader implements Uploader
{
    /**
     * @return array
     */
    public function upload(): array
    {
        $this->checkUploadFile();

        $this->createUploadedFile();

        $this->uploadedTime();

        return $this->allUploadInfo();
    }

    /**
     *
     */
    protected function createUploadedFile(): void
    {
        $this->pathComponent->createIfNotExists();

        if (!move_uploaded_file($this->uploadFile->getPath(), $this->pathComponent->getFullPath())) {
            throw new UploadException($this->uploadInfo['name'], UploadException::MOVE_TMP_FILE_ERR);
        }

        $this->uploadedFile = new File($this->pathComponent->getFullPath());
    }

    /**
     * @return void
     */
    protected function checkUploadFile(): void
    {
        $list = ['UploadedFile', 'UploadSelf', 'Size', 'Extension', 'Mime'];

        array_map(function ($value) {
            call_user_func("check{$value}");
        }, $list);
    }
}