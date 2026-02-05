<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;

describe('Entity toggle configuration', function () {
    it('does not create region_id column when regions disabled', function () {
        expect(Schema::hasColumn('countries', 'region_id'))->toBeFalse();
    });

    it('does not create states table when states disabled', function () {
        expect(Schema::hasTable('states'))->toBeFalse();
    });

    it('does not create cities table when cities disabled', function () {
        expect(Schema::hasTable('cities'))->toBeFalse();
    });

    it('still creates countries table when countries enabled', function () {
        expect(Schema::hasTable('countries'))->toBeTrue();
    });
});
