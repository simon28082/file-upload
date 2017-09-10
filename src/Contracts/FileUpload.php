<?php

namespace CrCms\Upload\Contracts;

/**
 * Interface FileUpload
 *
 * @package CrCms\Upload\Contracts
 */
interface FileUpload
{
    /**
     * @param array $file
     * @return array
     */
    public function upload(array $file): array;
}