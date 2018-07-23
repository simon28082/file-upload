<?php

namespace CrCms\Upload\Drivers;

/**
 * Class AbstractUpload
 * @package CrCms\Upload\Drivers
 */
/**
 * Class AbstractUpload
 * @package CrCms\Upload\Drivers
 */
abstract class AbstractUpload
{
    /**
     * @var array
     */
    protected $config;

    /**
     * AbstractUpload constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }
}