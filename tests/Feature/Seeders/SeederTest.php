<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Raiolanetworks\Atlas\Models\City;
use Raiolanetworks\Atlas\Models\Country;
use Raiolanetworks\Atlas\Models\Currency;
use Raiolanetworks\Atlas\Models\Language;
use Raiolanetworks\Atlas\Models\Region;
use Raiolanetworks\Atlas\Models\State;
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

describe('StatesSeeder', function () {
    beforeEach(function () {
        $this->artisan('atlas:regions');
        $this->artisan('atlas:subregions');
        $this->artisan('atlas:currencies');
        $this->artisan('atlas:countries');
    });

    it('populates the states table', function () {
        $this->artisan('atlas:states')->assertSuccessful();

        expect(State::count())->toBeGreaterThan(0);
    });

    it('creates states with foreign key to countries', function () {
        $this->artisan('atlas:states')->assertSuccessful();

        $state = State::whereNotNull('country_id')->first();

        expect($state)->not->toBeNull();
    });
});

describe('CitiesSeeder', function () {
    beforeEach(function () {
        $this->artisan('atlas:regions');
        $this->artisan('atlas:subregions');
        $this->artisan('atlas:currencies');
        $this->artisan('atlas:countries');
        $this->artisan('atlas:states');
    });

    it('populates the cities table', function () {
        $this->artisan('atlas:cities')->assertSuccessful();

        expect(City::count())->toBeGreaterThan(0);
    });

    it('creates cities with foreign keys', function () {
        $this->artisan('atlas:cities')->assertSuccessful();

        $city = City::whereNotNull('country_id')
            ->whereNotNull('state_id')
            ->first();

        expect($city)->not->toBeNull();
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
