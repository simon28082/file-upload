<?php

namespace CrCms\Upload\Traits;

/**
 * Class DirectoryTrait
 *
 * @package CrCms\Upload\Traits
 */
trait DirectoryTrait
{
    /**
     * @var int
     */
    protected $hashDirLayer = 2;

    /**
     * set Hash dir layer
     *
     * @param int $layer
     * @return DirectoryTrait
     */
    public function setHashDirLayer(int $layer): self
    {
        $this->hashDirLayer = $layer;
        return $this;
    }

    /**
     * Set hash dir
     *
     * @param string $name
     * @param integer $hashDirLayer
     * @author simon
     */
    public function getHashDir(string $name): string
    {
        $dirs = '';

        if ($this->hashDirLayer > 0) {
            $name = sha1($name);
            $length = strlen($name);

            for ($i = 0; $i < $length; $i++) {
                if ($i + 1 > $this->hashDirLayer) {
                    break;
                }
                $dirs .= substr($name, $i, 1) . '/';
            }
        }

        return $dirs;
    }

    /**
     * Create dir
     *
     * @param string $dir
     * @param number $mode
     * @return boolean
     */
    public function createDir(string $dir, int $mode = 0755): bool
    {
        if (is_dir($dir) || @mkdir($dir, $mode)) return true;
        if (!@$this->createDir(dirname($dir), $mode)) return false;
        return @mkdir($dir, $mode);
    }
}