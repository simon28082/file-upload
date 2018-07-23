<?php

namespace CrCms\Upload\Contracts;

/**
 * Interface Resolve
 * @package CrCms\Upload\Contracts
 */
interface Resolver
{
    /**
     * @param array $files
     * @return array
     */
    public function resolve(array $files): array;
}