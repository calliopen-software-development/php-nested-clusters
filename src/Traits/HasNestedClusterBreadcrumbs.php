<?php

namespace Calliopen\PhpNestedClusters\Traits;

use Filament\Facades\Filament;
use Illuminate\Support\Str;

trait HasNestedClusterBreadcrumbs
{
    public static function unshiftClusterBreadcrumbs(array $breadcrumbs): array
    {
        $namespace = static::class;
        $namespaceParts = collect(explode('\\', $namespace));

        $clusterIndex = $namespaceParts->search('Clusters');

        if ($clusterIndex === false) {
            return $breadcrumbs;
        }

        $clusterParts = $namespaceParts->slice($clusterIndex + 1);

        $path = '';
        $dynamicBreadcrumbs = [];

        foreach ($clusterParts as $index => $part) {
            if ($part === class_basename(static::class)) {
                break;
            }

            $clusterClass = implode('\\', array_slice($namespaceParts->toArray(), 0, $clusterIndex + 1 + $index + 1));
            $label = null;
            if (class_exists($clusterClass) && method_exists($clusterClass, 'label')) {
                $label = trim($clusterClass::label());
            }

            if (!$label) {
                $label = Str::of($part)
                    ->replaceLast('Cluster', '')
                    ->kebab()
                    ->replace('-', ' ')
                    ->title();
            }

            $slug = Str::kebab($part);
            $path .= '/' . $slug;

            $url = rtrim(Filament::getUrl(), '/') . '/' . ltrim($path, '/');

            $dynamicBreadcrumbs[$url] = $label;
        }

        $dynamicBreadcrumbs[static::getUrl()] = static::getClusterBreadcrumb();
        
        return array_merge($dynamicBreadcrumbs, $breadcrumbs);
    }
}