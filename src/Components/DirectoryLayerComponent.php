<?php

namespace CrCms\Upload\Components;

/**
 * Class DirectoryLayerComponent
 * @package CrCms\Upload\Components
 */
class DirectoryLayerComponent extends AbstractComponent
{
    /**
     * @var int
     */
    protected $layer = 2;

    protected $name;

//    protected $path;
//
//    protected $fullPath;

    public function __construct(string $name,int $layer = 2)
    {
        $this->setName($name);
        $this->setLayer($layer);
//        $this->setPath($path);
    }

//    public function setPath(string $path)
//    {
//        $this->path = $path;
//        return $this;
//    }

    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param int $layer
     * @return DirectoryLayerComponent
     */
    public function setLayer(int $layer): self
    {
        $this->hashDirLayer = $layer;

        return $this;
    }

    /**
     * @return string
     */
    public function getLayerDirectory(): string
    {
        $path = '';

        if ($this->layer > 0) {
            $prefix = $this->name;
            for ($i = 0; $i < strlen($prefix); $i++) {
                if ($i + 1 > $this->layer) {
                    break;
                }
                $path .= substr($prefix, $i, 1) . '/';
            }
        }

        return $path;
    }
//
//    public function fullPath(): string
//    {
//        return $this->path . DIRECTORY_SEPARATOR . $this->layerDirectory();
//    }
//
//    /**
//     * @param string $dir
//     * @param int $mode
//     * @return bool
//     */

}