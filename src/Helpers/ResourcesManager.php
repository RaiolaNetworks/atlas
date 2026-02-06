<?php

declare(strict_types=1);

namespace Raiolanetworks\Atlas\Helpers;

use InvalidArgumentException;

class ResourcesManager
{
    public const DATA_RESOURCES = [
        'regions'    => [
            'package-path'                             => __DIR__ . '/../../resources/json/regions.json',
            'overridden-client-project-resources-path' => 'vendor/atlas/json/regions.json',
        ],
        'subregions' => [
            'package-path'                             => __DIR__ . '/../../resources/json/subregions.json',
            'overridden-client-project-resources-path' => 'vendor/atlas/json/subregions.json',
        ],
        'countries'  => [
            'package-path'                             => __DIR__ . '/../../resources/json/countries.json',
            'overridden-client-project-resources-path' => 'vendor/atlas/json/countries.json',
        ],
        'states'     => [
            'package-path'                             => __DIR__ . '/../../resources/json/states.json',
            'overridden-client-project-resources-path' => 'vendor/atlas/json/states.json',
        ],
        'cities'     => [
            'package-path'                             => __DIR__ . '/../../resources/json/cities.json',
            'overridden-client-project-resources-path' => 'vendor/atlas/json/cities.json',
        ],
        'currencies' => [
            'package-path'                             => __DIR__ . '/../../resources/json/currencies.json',
            'overridden-client-project-resources-path' => 'vendor/atlas/json/currencies.json',
        ],
        'languages'  => [
            'package-path'                             => __DIR__ . '/../../resources/json/languages.json',
            'overridden-client-project-resources-path' => 'vendor/atlas/json/languages.json',
        ],
        // Timezones are embedded inside countries.json, not a separate file
        'timezones'  => [
            'package-path'                             => __DIR__ . '/../../resources/json/countries.json',
            'overridden-client-project-resources-path' => 'vendor/atlas/json/countries.json',
        ],
    ];

    public static function getResourcePath(string $resource): string
    {
        $path = self::getOverriddenClientProjectResourcesPath($resource);

        return file_exists($path) ? $path : self::getPackagePath($resource);
    }

    public static function getPackagePath(string $resource): string
    {
        if (! self::checkIfResourceKeyExists($resource)) {
            throw new InvalidArgumentException("Resource {$resource} not found");
        }

        return self::DATA_RESOURCES[$resource]['package-path'];
    }

    public static function getOverriddenClientProjectResourcesPath(string $resource): string
    {
        if (! self::checkIfResourceKeyExists($resource)) {
            throw new InvalidArgumentException("Resource {$resource} not found");
        }

        return resource_path(self::DATA_RESOURCES[$resource]['overridden-client-project-resources-path']);
    }

    private static function checkIfResourceKeyExists(string $resource): bool
    {
        return array_key_exists($resource, self::DATA_RESOURCES);
    }
}
