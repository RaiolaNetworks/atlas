<?php

declare(strict_types=1);

use Raiolanetworks\Atlas\Helpers\ResourcesManager;

it('returns valid package path for existing resources', function (string $resource) {
    $path = ResourcesManager::getPackagePath($resource);

    expect($path)->toBeString()
        ->and(file_exists($path))->toBeTrue();
})->with(['regions', 'subregions', 'countries', 'states', 'cities', 'currencies', 'languages']);

it('throws exception for invalid resource', function () {
    ResourcesManager::getPackagePath('invalid-resource');
})->throws(Exception::class, 'Resource invalid-resource not found');

it('returns package path when client override does not exist', function () {
    $path = ResourcesManager::getResourcePath('regions');

    expect($path)->toBe(ResourcesManager::getPackagePath('regions'));
});

it('throws exception for invalid resource in getResourcePath', function () {
    ResourcesManager::getResourcePath('invalid-resource');
})->throws(Exception::class, 'Resource invalid-resource not found');

it('returns override path containing vendor/atlas/json/', function (string $resource) {
    $path = ResourcesManager::getOverriddenClientProjectResourcesPath($resource);

    expect($path)->toContain('vendor/atlas/json/');
})->with(['regions', 'subregions', 'countries', 'states', 'cities', 'currencies', 'languages', 'timezones']);

it('returns override path ending with the correct resource file', function (string $resource, string $expectedFile) {
    $path = ResourcesManager::getOverriddenClientProjectResourcesPath($resource);

    expect($path)->toEndWith("vendor/atlas/json/{$expectedFile}");
})->with([
    ['regions', 'regions.json'],
    ['subregions', 'subregions.json'],
    ['countries', 'countries.json'],
    ['states', 'states.json'],
    ['cities', 'cities.json'],
    ['currencies', 'currencies.json'],
    ['languages', 'languages.json'],
    ['timezones', 'countries.json'],
]);
