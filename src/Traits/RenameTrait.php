<?php

namespace CrCms\Upload\Traits;

/**
 * Class RenameTrait
 *
 * @package CrCms\Upload\Traits
 * @author simon
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
    public function getNewName(string $oldName = ''): string
    {
        if (empty($this->newName) && !empty($oldName)) {
            return $this->getDefaultNewName($oldName);
        }

        return $this->newName;
    }

    /**
     * @param string $oldName
     * @param callable|null $callable
     */
    public function setNewName(string $oldName, callable $callable = null): self
    {
        $this->newName = ($this->isRename && is_callable($callable)) ? $callable($oldName) : $oldName;

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