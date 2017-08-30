<?php

namespace CrCms\Upload\Contracts;

/**
 * Interface FileUpload
 *
 * @package CrCms\Upload\Contracts
 * @author simon
 */
interface FileUpload
{
    /**
     * @return array
     */
    public function upload(): array;
}