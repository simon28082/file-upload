<?php

namespace CrCms\Upload\Drivers\Resolver;

use CrCms\Upload\Contracts\Resolver;

/**
 * Class DefaultResolve
 * @package CrCms\Upload\Drivers\Resolve
 */
class DefaultResolver implements Resolver
{
    /**
     * @param array $files
     * @return array
     */
    public function resolve(array $files): array
    {
        $formatFiles = [];

        foreach ($files as $key => $values) {
            if (is_array($values['name'])) {
                foreach ($values['name'] as $k => $vo) {
                    if (empty($vo)) continue;
                    $temp['name'] = $vo;
                    $temp['type'] = $values['type'][$k];
                    $temp['tmp_name'] = $values['tmp_name'][$k];
                    $temp['error'] = $values['error'][$k];
                    $temp['size'] = $values['size'][$k];
                    $temp['__name'] = $key;
                    $formatFiles[] = $temp;
                }
            } else {
                if (empty($values['name'])) continue;
                $values['__name'] = $key;
                $formatFiles[] = $values;
            }
        }

        return $formatFiles;
    }
}