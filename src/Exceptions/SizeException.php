<?php

namespace CrCms\Upload\Exceptions;

use RuntimeException;

/**
 * Class SizeException
 *
 * @package CrCms\Upload\Exceptions
 * @author simon
 */
class SizeException extends RuntimeException
{
    /**
     * SizeException constructor.
     * @param string $filename
     */
    public function __construct($filename)
    {
        parent::__construct(sprintf('File “%s” size exceeds the limit ', $filename));
    }
}