<?php

namespace CrCms\Upload\Contracts;

/**
 * Interface FileUpload
 *
 * @package CrCms\Upload\Contracts
 */
interface Uploader
{
    /**
     * @return array
     */
    public function upload(): array;
}