<?php

/**
 * @author simon <crcms@crcms.cn>
 * @datetime 2018-07-22 12:36
 * @link http://crcms.cn/
 * @copyright Copyright &copy; 2018 Rights Reserved CRCMS
 */

namespace CrCms\Upload\Components;

/**
 * Class PathComponent
 * @package CrCms\Upload\Components
 */
class PathComponent
{
    protected $path;

    protected $directoryLayer;

    protected $fullPath;

    public function __construct(string $path, ?DirectoryLayerComponent $component = null)
    {
        $this->setPath($path);
        $component ? $this->setLayerComponent($component) : null;
        $this->setFullPath($this->fullPath());
    }

    public function setFullPath(string $path)
    {
        $this->fullPath = $path;
        return $this;
    }

    public function getFullPath(): string
    {
        return $this->fullPath;
    }

    public function setPath(string $path)
    {
        $this->path = rtrim($path, '/');
        return $this;
    }

    public function setLayerComponent(DirectoryLayerComponent $component)
    {
        $this->directoryLayer = $component;
        return $this;
    }

    public function fullPath(): string
    {
        $path = $this->path;

        if ($this->directoryLayer instanceof DirectoryLayerComponent) {
            $path = $path . DIRECTORY_SEPARATOR . $this->directoryLayer->getLayerDirectory();
        }

        return $path;
    }

    public function exists(): bool
    {
        return is_dir($this->fullPath);
    }

    public function create(int $mode = 0755): bool
    {
        return @mkdir($this->fullPath, $mode, true);
    }

    public function createIfNotExists(): bool
    {
        if (!$this->exists()) {
            return $this->create();
        }

        return true;
    }
}