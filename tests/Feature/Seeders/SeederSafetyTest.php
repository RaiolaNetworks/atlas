<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Raiolanetworks\Atlas\Models\Country;
use Raiolanetworks\Atlas\Models\Timezone;

describe('FK constraints are restored after seeding', function () {
    it('leaves foreign key constraints enabled after a successful seed', function () {
        $this->artisan('atlas:regions')->assertSuccessful();

        $fk = DB::select('PRAGMA foreign_keys');

        expect($fk[0]->foreign_keys)->toBe(1);
    });

    it('leaves foreign key constraints enabled after re-seeding', function () {
        $this->artisan('atlas:regions')->assertSuccessful();
        $this->artisan('atlas:regions')->assertSuccessful();

        $fk = DB::select('PRAGMA foreign_keys');

        expect($fk[0]->foreign_keys)->toBe(1);
    });

    it('leaves foreign key constraints enabled after seeding entity with pivot', function () {
        $this->artisan('atlas:regions')->assertSuccessful();
        $this->artisan('atlas:subregions')->assertSuccessful();
        $this->artisan('atlas:currencies')->assertSuccessful();
        $this->artisan('atlas:countries')->assertSuccessful();
        $this->artisan('atlas:timezones')->assertSuccessful();

        $fk = DB::select('PRAGMA foreign_keys');

        expect($fk[0]->foreign_keys)->toBe(1);
    });
});

describe('Truncate with foreign key relationships', function () {
    beforeEach(function () {
        $this->artisan('atlas:regions');
        $this->artisan('atlas:subregions');
        $this->artisan('atlas:currencies');
        $this->artisan('atlas:countries');
    });

    it('re-seeds countries without FK violation despite related states', function () {
        $this->artisan('atlas:states')->assertSuccessful();
        $countBefore = Country::count();

        $this->artisan('atlas:countries')->assertSuccessful();

        expect(Country::count())->toBe($countBefore);
    });

    it('re-seeds timezones without FK violation on pivot table', function () {
        $this->artisan('atlas:timezones')->assertSuccessful();
        $countBefore = Timezone::count();

        $this->artisan('atlas:timezones')->assertSuccessful();

        expect(Timezone::count())->toBe($countBefore);
        expect(DB::table('country_timezone')->count())->toBeGreaterThan(0);
    });
});
