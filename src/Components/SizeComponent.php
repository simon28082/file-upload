<?php

namespace CrCms\Upload\Components;

/**
 * Class SizeComponent
 * @package CrCms\Upload\Traits
 */
class SizeComponent extends AbstractComponent
{
    /**
     * @var int
     */
    protected $fileSize = 1024 * 1024 * 2;

    /**
     * SizeComponent constructor.
     * @param $fileSize
     */
    public function __construct($fileSize)
    {
        $this->setFileSize($fileSize);
    }

    /**
     * @param int|string $fileSize
     * @return SizeComponent
     */
    public function setFileSize($fileSize): self
    {
        $this->fileSize = $this->sizeToByte($fileSize);

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
     * @param int $size
     * @return bool
     */
    public function checkSize(int $size): bool
    {
        return $this->getFileSize() >= $size;
    }

    /**
     * @param $fileSize
     * @return int
     */
    protected function sizeToByte($fileSize): int
    {
        if (is_numeric($fileSize)) return $fileSize;

        $unit = strtoupper(substr($fileSize, -2, 2));

        $fileSize = rtrim($fileSize, $unit);

        switch ($unit) {
            case 'KB':
                $fileSize = $fileSize * pow(2, 10);
                break;
            case 'MB':
                $fileSize = $fileSize * pow(2, 20);
                break;
            case 'GB':
                $fileSize = $fileSize * pow(2, 30);
                break;
            case 'TB':
                $fileSize = $fileSize * pow(2, 40);
                break;
            case 'PB':
                $fileSize = $fileSize * pow(2, 50);
                break;
            default:
                $fileSize = 0;
        }

        return $fileSize;
    }
}