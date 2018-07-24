<?php

namespace CrCms\Upload\Components;

use Closure;

/**
 * Class RenameComponent
 * @package CrCms\Upload\Components
 */
class RenameComponent extends AbstractComponent
{
    /**
     * @var bool
     */
    protected $isRename = true;

    /**
     * @var string
     */
    protected $oldName;

    /**
     * @var Closure
     */
    protected $renameCallback;

    /**
     * RenameComponent constructor.
     * @param bool $isRename
     * @param string $oldName
     * @param Closure|null $renameCallback
     */
    public function __construct(bool $isRename, string $oldName, ?Closure $renameCallback = null)
    {
        $this->setRename($isRename);
        $this->setOldName($oldName);
        $this->setRenameCallback($renameCallback);
    }

    /**
     * @param string $oldName
     * @return RenameComponent
     */
    public function setOldName(string $oldName): self
    {
        $this->oldName = $oldName;
        return $this;
    }

    /**
     * @param Closure|null $renameCallback
     * @return RenameComponent
     */
    public function setRenameCallback(?Closure $renameCallback = null): self
    {
        $this->renameCallback = $renameCallback;

        return $this;
    }

    /**
     * @param bool $isRename
     * @return RenameComponent
     */
    public function setRename(bool $isRename): self
    {
        $this->isRename = $isRename;

        return $this;
    }

    /**
     * @return bool
     */
    public function getRename(): bool
    {
        return $this->isRename;
    }

    /**
     * @return string
     */
    public function getNewName(): string
    {
        return $this->isRename ?
            ($this->renameCallback ? call_user_func($this->renameCallback, $this->oldName) : $this->getDefaultNewName($this->oldName)) :
            $this->oldName;
    }

    /**
     * @param string $oldName
     * @return string
     */
    protected function getDefaultNewName(string $oldName): string
    {
        return sha1(uniqid('AXEDF365fa_')) . mt_rand(1000, 9999) . '.' . pathinfo($oldName, PATHINFO_EXTENSION);
    }
}