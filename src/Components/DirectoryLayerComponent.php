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

    /**
     * @var string
     */
    protected $name;

    /**
     * DirectoryLayerComponent constructor.
     * @param string $name
     * @param int $layer
     */
    public function __construct(string $name, int $layer = 2)
    {
        $this->setName($name);
        $this->setLayer($layer);
    }

    /**
     * @param string $name
     * @return $this
     */
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
        $this->layer = $layer;

        return $this;
    }

    /**
     * @return string
     */
    public function getLayerDirectory(): string
    {
        $path = '';

        if ($this->layer > 0) {
            $prefix = md5($this->name);
            for ($i = 0; $i < strlen($prefix); $i++) {
                if ($i + 1 > $this->layer) {
                    break;
                }
                $path .= substr($prefix, $i, 1) . '/';
            }
        }

        return rtrim($path, '/');
    }
}