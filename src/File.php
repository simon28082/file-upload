<?php

namespace CrCms\Upload;

/**
 * Class File
 *
 * @package CrCms\Upload
 * @author simon
 */
class File
{
    /**
     * @var \SplFileInfo
     */
    protected $splFileInfo;

    /**
     * File constructor.
     * @param string $path
     */
    public function __construct(string $filePath)
    {
        $this->splFileInfo = new \SplFileInfo($filePath);
    }

    /**
     * @param string $filePath
     * @return File
     */
    /*public function setFile(string $filePath): self
    {

        return $this;
    }*/

    /**
     * @return string
     */
    public function getMime(): string
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        return finfo_file($finfo, $this->splFileInfo->getPathname());
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        if (method_exists($this->splFileInfo, $name)) {
            return call_user_func_array([$this->splFileInfo, $name], $arguments);
        }

        throw new \BadMethodCallException("method [$name] is not exists");
    }
}