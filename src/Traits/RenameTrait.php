<?php

namespace CrCms\Upload\Traits;

/**
 * Class RenameTrait
 *
 * @package CrCms\Upload\Traits
 */
trait RenameTrait
{
    /**
     * @var bool
     */
    protected $isRename = true;

    /**
     * @var string
     */
    protected $newName;

    /**
     * set is rename
     * @param bool $isRename
     * @return RenameTrait
     */
    public function setRename(bool $isRename): self
    {
        $this->isRename = $isRename;
        return $this;
    }

    /**
     * get is rename
     * @return bool
     */
    public function getRename(): bool
    {
        return $this->isRename;
    }

    /**
     * @param string $oldName
     * @return string
     */
    public function getNewName(): string
    {
        return $this->newName;
    }

    /**
     * @param string $oldName
     * @param callable|null $callable
     */
    public function setNewName(string $oldName, callable $callable = null): self
    {
        if ($this->isRename) {
            $this->newName = is_callable($callable) ? $callable($oldName) : $this->getDefaultNewName($oldName);
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
        return sha1(uniqid('AXEDF365fa_')) . mt_rand(1, 9999999) . '.' . pathinfo($oldName, PATHINFO_EXTENSION);
    }
}