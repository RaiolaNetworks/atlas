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
