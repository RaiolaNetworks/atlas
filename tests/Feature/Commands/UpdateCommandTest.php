<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Raiolanetworks\Atlas\Models\Region;

describe('atlas:update command', function () {
    it('re-seeds all enabled entities', function () {
        $this->artisan('atlas:update')
            ->assertSuccessful()
            ->expectsOutputToContain('All enabled entities have been updated successfully.');

        expect(Region::count())->toBeGreaterThan(0);
    });

    it('fails when required tables do not exist', function () {
        Schema::dropIfExists('languages');

        $this->artisan('atlas:update')
            ->assertFailed()
            ->expectsOutputToContain('tables are missing');
    });

    it('respects disabled entities', function () {
        config()->set('atlas.entities.languages', false);

        $this->artisan('atlas:update')
            ->assertSuccessful()
            ->doesntExpectOutputToContain('Seeding languages');
    });
});
