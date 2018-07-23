<?php

namespace CrCms\Upload\Components;

/**
 * Class ExtensionComponent
 * @package CrCms\Upload\Components
 */
class ExtensionComponent extends AbstractComponent
{
    /**
     * 允许的文件扩展名
     *
     * @var array
     */
    protected $extensions = ['jpg', 'jpeg', 'gif', 'png', 'bmp'];

    /**
     * 是否验证文件扩展名
     *
     * @var boolean
     */
    protected $checkExtension = true;

    /**
     * ExtensionComponent constructor.
     * @param array $extensions
     * @param bool $isCheck
     */
    public function __construct(array $extensions, bool $isCheck = true)
    {
        $this->setExtensions($extensions);
        $this->setCheckExtension($isCheck);
    }

    /**
     * 是否验证扩展名
     *
     * @param bool $isCheck
     * @return ExtensionComponent
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
     * @param array $extensions
     * @return ExtensionComponent
     */
    public function setExtensions(array $extensions): self
    {
        $this->extensions = $extensions;

        return $this;
    }

    /**
     * @param string $extension
     * @return ExtensionComponent
     */
    public function addExtension(string $extension): self
    {
        if (!in_array($extension, $this->extensions, true)) {
            $this->extensions[] = $extension;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getExtensions(): array
    {
        return $this->extensions;
    }

    /**
     * @param string $extension
     * @return bool
     */
    public function checkExtension(string $extension): bool
    {
        if ($this->checkExtension) {
            return in_array(strtolower($extension), $this->getExtensions(), true);
        }

        return true;
    }
}