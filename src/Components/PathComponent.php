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
    /**
     * @var string
     */
    protected $path;

    /**
     * @var DirectoryLayerComponent
     */
    protected $directoryLayer;

    /**
     * @var string
     */
    protected $fullPath;

    /**
     * @var RenameComponent
     */
    protected $rename;

    /**
     * PathComponent constructor.
     * @param string $path
     * @param RenameComponent $rename
     * @param DirectoryLayerComponent $directoryLayer
     */
    public function __construct(string $path, RenameComponent $rename, DirectoryLayerComponent $directoryLayer)
    {
        $this->setPath($path);
        $this->setRename($rename);
        $this->setLayerComponent($directoryLayer);
        $this->setFullPath($this->fullPath());
    }

    /**
     * @param RenameComponent $rename
     * @return PathComponent
     */
    public function setRename(RenameComponent $rename): self
    {
        $this->rename = $rename;
        return $this;
    }

    /**
     * @param string $path
     * @return PathComponent
     */
    public function setFullPath(string $path): self
    {
        $this->fullPath = $path;
        return $this;
    }

    /**
     * @return string
     */
    public function getFullPath(): string
    {
        return $this->fullPath;
    }

    /**
     * @param string $path
     * @return PathComponent
     */
    public function setPath(string $path): self
    {
        $this->path = rtrim($path, '/');
        return $this;
    }

    /**
     * @param DirectoryLayerComponent $component
     * @return PathComponent
     */
    public function setLayerComponent(DirectoryLayerComponent $component): self
    {
        $this->directoryLayer = $component;
        return $this;
    }

    /**
     * @return string
     */
    public function fullPath(): string
    {
        $path = $this->path;

        $path = $path . DIRECTORY_SEPARATOR . $this->directoryLayer->getLayerDirectory();

        return $path . '/' . $this->rename->getNewName();
    }

    /**
     * @return bool
     */
    public function exists(): bool
    {
        return is_dir(dirname($this->fullPath));
    }

    /**
     * @param int $mode
     * @return bool
     */
    public function create(int $mode = 0755): bool
    {
        return @mkdir(dirname($this->fullPath), $mode, true);
    }

    /**
     * @return bool
     */
    public function createIfNotExists(): bool
    {
        if (!$this->exists()) {
            return $this->create();
        }

        return true;
    }
}