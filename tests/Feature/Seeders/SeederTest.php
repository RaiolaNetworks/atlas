<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Raiolanetworks\Atlas\Models\Country;
use Raiolanetworks\Atlas\Models\Currency;
use Raiolanetworks\Atlas\Models\Language;
use Raiolanetworks\Atlas\Models\Region;
use Raiolanetworks\Atlas\Models\Subregion;
use Raiolanetworks\Atlas\Models\Timezone;

describe('RegionsSeeder', function () {
    it('populates the regions table', function () {
        $this->artisan('atlas:regions')->assertSuccessful();

        expect(Region::count())->toBeGreaterThan(0);
    });

    it('creates the expected number of regions', function () {
        $this->artisan('atlas:regions')->assertSuccessful();

        expect(Region::count())->toBe(6);
    });
});

describe('LanguagesSeeder', function () {
    it('populates the languages table', function () {
        $this->artisan('atlas:languages')->assertSuccessful();

        expect(Language::count())->toBeGreaterThan(0);
    });
});

describe('CurrenciesSeeder', function () {
    it('populates the currencies table', function () {
        $this->artisan('atlas:currencies')->assertSuccessful();

        expect(Currency::count())->toBeGreaterThan(0);
    });

    it('uses string as primary key', function () {
        $this->artisan('atlas:currencies')->assertSuccessful();

        $currency = Currency::first();

        expect($currency->getKey())->toBeString();
    });
});

describe('SubregionsSeeder', function () {
    beforeEach(function () {
        $this->artisan('atlas:regions');
    });

    it('populates the subregions table', function () {
        $this->artisan('atlas:subregions')->assertSuccessful();

        expect(Subregion::count())->toBeGreaterThan(0);
    });

    it('creates subregions with foreign key to regions', function () {
        $this->artisan('atlas:subregions')->assertSuccessful();

        $subregion = Subregion::first();

        expect($subregion->region_id)->not->toBeNull();
    });
});

describe('CountriesSeeder', function () {
    beforeEach(function () {
        $this->artisan('atlas:regions');
        $this->artisan('atlas:subregions');
        $this->artisan('atlas:currencies');
    });

    it('populates the countries table', function () {
        $this->artisan('atlas:countries')->assertSuccessful();

        expect(Country::count())->toBeGreaterThan(0);
    });

    it('creates countries with foreign keys', function () {
        $this->artisan('atlas:countries')->assertSuccessful();

        $country = Country::whereNotNull('region_id')
            ->whereNotNull('subregion_id')
            ->first();

        expect($country)->not->toBeNull();
    });
});

describe('TimezonesSeeder', function () {
    beforeEach(function () {
        $this->artisan('atlas:regions');
        $this->artisan('atlas:subregions');
        $this->artisan('atlas:currencies');
        $this->artisan('atlas:countries');
    });

    it('populates the timezones table', function () {
        $this->artisan('atlas:timezones')->assertSuccessful();

        expect(Timezone::count())->toBeGreaterThan(0);
    });

    it('creates pivot records for country_timezone', function () {
        $this->artisan('atlas:timezones')->assertSuccessful();

        $pivotCount = DB::table('country_timezone')->count();

        expect($pivotCount)->toBeGreaterThan(0);
    });
});

describe('Seeder truncation', function () {
    it('truncates table before re-seeding (idempotent)', function () {
        $this->artisan('atlas:regions')->assertSuccessful();
        $countFirst = Region::count();

        $this->artisan('atlas:regions')->assertSuccessful();
        $countSecond = Region::count();

        expect($countFirst)->toBe($countSecond);
    });
});
