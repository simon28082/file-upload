<?php

namespace CrCms\Upload\Traits;

/**
 * Class SizeTrait
 *
 * @package CrCms\Upload\Traits
 * @author simon
 */
trait SizeTrait
{
    /**
     * @var int
     */
    protected $fileSize = 1024 * 1024 * 2;

    /**
     * @param int $fileSize
     * @return SizeTrait
     */
    public function setFileSize(int $fileSize): self
    {
        $this->fileSize = $fileSize;
        return $this;
    }

    /**
     * @return int
     */
    public function getFileSize(): int
    {
        return $this->fileSize;
    }

    /**
     * size cover byte
     * @return int
     */
    protected function sizeToByte(): int
    {
        if (is_numeric($this->fileSize)) return $this->fileSize;

        $unit = strtoupper(substr($this->fileSize, -2, 2));

        $this->fileSize = rtrim($this->fileSize, $unit);

        switch ($unit) {
            case 'KB':
                $this->fileSize = $this->fileSize * pow(2, 10);
                break;
            case 'MB':
                $this->fileSize = $this->fileSize * pow(2, 20);
                break;
            case 'GB':
                $this->fileSize = $this->fileSize * pow(2, 30);
                break;
            case 'TB':
                $this->fileSize = $this->fileSize * pow(2, 40);
                break;
            case 'PB':
                $this->fileSize = $this->fileSize * pow(2, 50);
                break;
            default:
                $this->fileSize = 0;
        }

        return $this->fileSize;
    }
}