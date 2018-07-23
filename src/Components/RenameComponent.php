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
     * @var
     */
    protected $newName;


    /**
     * @var Closure
     */
    protected $renameCallback;

    public function __construct(bool $isRename, ?Closure $renameCallback = null)
    {
        $this->setRename($isRename);
        $this->setRenameCallback($renameCallback);
    }

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
        return $this->newName;
    }

    /**
     * @param string $oldName
     * @param callable|null $callable
     * @return RenameComponent
     */
    public function setNewName(string $oldName): self
    {
        if ($this->isRename) {
            $this->newName = $this->renameCallback ? call_user_func($this->renameCallback, $oldName) : $this->getDefaultNewName($oldName);
        } else {
            $this->newName = $oldName;
        }

        return $this;
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