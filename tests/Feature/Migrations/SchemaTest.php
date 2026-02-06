<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;

describe('Countries table schema', function () {
    it('has region_id as nullable column', function () {
        $columns  = Schema::getColumns('countries');
        $regionId = collect($columns)->firstWhere('name', 'region_id');

        expect($regionId)->not->toBeNull();
        expect($regionId['nullable'])->toBeTrue();
    });

    it('has subregion_id as nullable column', function () {
        $columns     = Schema::getColumns('countries');
        $subregionId = collect($columns)->firstWhere('name', 'subregion_id');

        expect($subregionId)->not->toBeNull();
        expect($subregionId['nullable'])->toBeTrue();
    });
});
