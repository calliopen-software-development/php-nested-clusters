<?php

namespace Calliopen\PhpNestedClusters\Traits;

trait HasNestedClusterSlugs
{
    public static function getSlug(): string
    {
        $clusterPath = static::$cluster ?? static::inferClusterPathFromClass();

        $slug = str($clusterPath)
            ->after('App\\Filament\\Clusters\\')
            ->replace('\\', '/')
            ->kebab()
            ->lower()
            ->value();

        return trim($slug . '/' . static::getResourceSlug(), '/');
    }

    protected static function getResourceSlug(): string
    {
        return str(class_basename(static::class))->beforeLast('Resource')->kebab();
    }

    protected static function inferClusterPathFromClass(): string
    {
        $fullClass = static::class;
        $segments = explode('\\', $fullClass);
        $clusterIndex = array_search('Clusters', $segments);

        if ($clusterIndex === false || !isset($segments[$clusterIndex + 1])) {
            return '';
        }

        return implode('\\', array_slice($segments, $clusterIndex + 1, -1));
    }
}