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

    it('has currency_code as nullable column', function () {
        $columns      = Schema::getColumns('countries');
        $currencyCode = collect($columns)->firstWhere('name', 'currency_code');

        expect($currencyCode)->not->toBeNull();
        expect($currencyCode['nullable'])->toBeTrue();
    });

    it('has region_name column instead of region', function () {
        $columns = Schema::getColumns('countries');

        expect(collect($columns)->firstWhere('name', 'region_name'))->not->toBeNull();
        expect(collect($columns)->firstWhere('name', 'region'))->toBeNull();
    });

    it('has subregion_name column instead of subregion', function () {
        $columns = Schema::getColumns('countries');

        expect(collect($columns)->firstWhere('name', 'subregion_name'))->not->toBeNull();
        expect(collect($columns)->firstWhere('name', 'subregion'))->toBeNull();
    });
});

describe('Foreign key cascade behavior', function () {
    it('has cascadeOnDelete for states.country_id', function () {
        $fk = collect(Schema::getForeignKeys('states'))
            ->first(fn (array $fk) => in_array('country_id', $fk['columns']));

        expect($fk)->not->toBeNull();
        expect($fk['on_delete'])->toBe('cascade');
    });

    it('has cascadeOnDelete for cities.state_id', function () {
        $fk = collect(Schema::getForeignKeys('cities'))
            ->first(fn (array $fk) => in_array('state_id', $fk['columns']));

        expect($fk)->not->toBeNull();
        expect($fk['on_delete'])->toBe('cascade');
    });

    it('has cascadeOnDelete for cities.country_id', function () {
        $fk = collect(Schema::getForeignKeys('cities'))
            ->first(fn (array $fk) => in_array('country_id', $fk['columns']));

        expect($fk)->not->toBeNull();
        expect($fk['on_delete'])->toBe('cascade');
    });

    it('has cascadeOnDelete for subregions.region_id', function () {
        $fk = collect(Schema::getForeignKeys('subregions'))
            ->first(fn (array $fk) => in_array('region_id', $fk['columns']));

        expect($fk)->not->toBeNull();
        expect($fk['on_delete'])->toBe('cascade');
    });

    it('has cascadeOnDelete for country_timezone.country_id', function () {
        $fk = collect(Schema::getForeignKeys('country_timezone'))
            ->first(fn (array $fk) => in_array('country_id', $fk['columns']));

        expect($fk)->not->toBeNull();
        expect($fk['on_delete'])->toBe('cascade');
    });

    it('has cascadeOnDelete for country_timezone.timezone_name', function () {
        $fk = collect(Schema::getForeignKeys('country_timezone'))
            ->first(fn (array $fk) => in_array('timezone_name', $fk['columns']));

        expect($fk)->not->toBeNull();
        expect($fk['on_delete'])->toBe('cascade');
    });
});
