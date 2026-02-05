<?php

declare(strict_types=1);

namespace Raiolanetworks\Atlas\Helpers;

use Exception;

class ResourcesManager
{
    public const DATA_RESOURCES = [
        'regions'    => [
            'package-path'                            => __DIR__ . '/../../resources/json/regions.json',
            'overrided-client-project-resources-path' => 'json/regions.json',
        ],
        'subregions' => [
            'package-path'                            => __DIR__ . '/../../resources/json/subregions.json',
            'overrided-client-project-resources-path' => 'json/subregions.json',
        ],

        'countries'  => [
            'package-path'                            => __DIR__ . '/../../resources/json/countries.json',
            'overrided-client-project-resources-path' => 'json/countries.json',
        ],
        'states'     => [
            'package-path'                            => __DIR__ . '/../../resources/json/states.json',
            'overrided-client-project-resources-path' => 'json/states.json',
        ],
        'cities'     => [
            'package-path'                            => __DIR__ . '/../../resources/json/cities.json',
            'overrided-client-project-resources-path' => 'json/cities.json',
        ],
        'currencies' => [
            'package-path'                            => __DIR__ . '/../../resources/json/currencies.json',
            'overrided-client-project-resources-path' => 'json/currencies.json',
        ],
        'languages'  => [
            'package-path'                            => __DIR__ . '/../../resources/json/languages.json',
            'overrided-client-project-resources-path' => 'json/languages.json',
        ],
        'timezones'  => [
            'package-path'                            => __DIR__ . '/../../resources/json/countries.json',
            'overrided-client-project-resources-path' => 'json/countries.json',
        ],
    ];

    public static function getResourcePath(string $resource): string
    {
        $path = self::getOverridedClientProjectResourcesPath($resource);

        return file_exists($path) ? $path : self::getPackagePath($resource);
    }

    public static function getPackagePath(string $resource): string
    {
        if (! self::checkIfResourceKeyExists($resource)) {
            throw new Exception("Resource {$resource} not found");
        }

        return self::DATA_RESOURCES[$resource]['package-path'];
    }

    public static function getOverridedClientProjectResourcesPath(string $resource): string
    {
        if (! self::checkIfResourceKeyExists($resource)) {
            throw new Exception("Resource {$resource} not found");
        }

        return resource_path(self::DATA_RESOURCES[$resource]['overrided-client-project-resources-path']);
    }

    private static function checkIfResourceKeyExists(string $resource): bool
    {
        return array_key_exists($resource, self::DATA_RESOURCES);
    }
}
