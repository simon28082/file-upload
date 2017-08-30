<?php

namespace CrCms\Upload\Traits;

/**
 * Class ExtensionTrait
 *
 * @package CrCms\Upload\Traits
 * @author simon
 */
trait ExtensionTrait
{
    /**
     * 允许的文件扩展名
     * @var array
     * @author simon
     */
    protected $extensions = ['jpg', 'jpeg', 'gif', 'png', 'bmp'];

    /**
     * 是否验证文件扩展名
     * @var boolean
     * @author simon
     */
    protected $checkExtension = true;

    /**
     * 是否验证扩展名
     * @param bool $isCheck
     * @return ExtensionTrait
     */
    public function setCheckExtension(bool $isCheck): self
    {
        $this->checkExtension = $isCheck;

        return $this;
    }

    /**
     * @return bool
     */
    public function getCheckExtension(): bool
    {
        return $this->checkExtension;
    }

    /**
     * set allow file extensions
     * @param array $extensions
     * @return ExtensionTrait
     */
    public function setExtensions(array $extensions): self
    {
        $this->extensions = $extensions;

        return $this;
    }

    /**
     * set allow file extension
     * @param string $extension
     * @return ExtensionTrait
     */
    public function setExtension(string $extension): self
    {
        $this->extensions[] = $extension;

        return $this;
    }

    /**
     * @return array
     */
    public function getExtensions(): array
    {
        return $this->extensions;
    }
}