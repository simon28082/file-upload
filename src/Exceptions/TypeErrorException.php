<?php

namespace CrCms\Upload\Exceptions;

use RuntimeException;

/**
 * Class TypeErrorException
 *
 * @package CrCms\Upload\Exceptions
 * @author simon
 */
class TypeErrorException extends RuntimeException
{
    /**
     * TypeErrorException constructor.
     * @param string $filename
     * @param int $type
     */
    public function __construct($filename, $type)
    {
        parent::__construct(sprintf("%s file %s is error ", $filename, $type));
    }
}