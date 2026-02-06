<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Raiolanetworks\Atlas\Models\City;
use Raiolanetworks\Atlas\Models\Country;
use Raiolanetworks\Atlas\Models\Currency;
use Raiolanetworks\Atlas\Models\Language;
use Raiolanetworks\Atlas\Models\Region;
use Raiolanetworks\Atlas\Models\State;
use Raiolanetworks\Atlas\Models\Subregion;
use Raiolanetworks\Atlas\Models\Timezone;

describe('Country relationships', function () {
    it('has belongsTo relationship with Region', function () {
        $country = new Country;

        expect($country->region())->toBeInstanceOf(BelongsTo::class);
    });

    it('has belongsTo relationship with Subregion', function () {
        $country = new Country;

        expect($country->subregion())->toBeInstanceOf(BelongsTo::class);
    });

    it('has belongsTo relationship with Currency', function () {
        $country = new Country;

        expect($country->currency())->toBeInstanceOf(BelongsTo::class);
    });

    it('has hasMany relationship with States', function () {
        $country = new Country;

        expect($country->states())->toBeInstanceOf(HasMany::class);
    });

    it('has hasMany relationship with Cities', function () {
        $country = new Country;

        expect($country->cities())->toBeInstanceOf(HasMany::class);
    });

    it('has belongsToMany relationship with Timezones', function () {
        $country = new Country;

        expect($country->timezones())->toBeInstanceOf(BelongsToMany::class);
    });

    it('uses correct FK config for currency()', function () {
        $relation = (new Country)->currency();

        expect($relation->getForeignKeyName())->toBe('currency_code')
            ->and($relation->getOwnerKeyName())->toBe('code');
    });

    it('uses correct FK config for timezones()', function () {
        $relation = (new Country)->timezones();

        expect($relation->getForeignPivotKeyName())->toBe('country_id')
            ->and($relation->getRelatedPivotKeyName())->toBe('timezone_name')
            ->and($relation->getTable())->toBe(config('atlas.country_timezone_pivot_tablename'));
    });

    it('uses correct FK config for region()', function () {
        $relation = (new Country)->region();

        expect($relation->getForeignKeyName())->toBe('region_id');
    });

    it('uses correct FK config for subregion()', function () {
        $relation = (new Country)->subregion();

        expect($relation->getForeignKeyName())->toBe('subregion_id');
    });
});

describe('Timezone relationships', function () {
    it('has belongsToMany relationship with Countries', function () {
        $timezone = new Timezone;

        expect($timezone->countries())->toBeInstanceOf(BelongsToMany::class);
    });

    it('uses string as primary key', function () {
        $timezone = new Timezone;

        expect($timezone->getKeyName())->toBe('zone_name')
            ->and($timezone->getKeyType())->toBe('string')
            ->and($timezone->getIncrementing())->toBeFalse();
    });
});

describe('Currency relationships', function () {
    it('has hasMany relationship with Countries', function () {
        $currency = new Currency;

        expect($currency->countries())->toBeInstanceOf(HasMany::class);
    });

    it('uses string as primary key', function () {
        $currency = new Currency;

        expect($currency->getKeyName())->toBe('code')
            ->and($currency->getKeyType())->toBe('string')
            ->and($currency->getIncrementing())->toBeFalse();
    });

    it('uses correct FK config for countries()', function () {
        $relation = (new Currency)->countries();

        expect($relation->getForeignKeyName())->toBe('currency_code')
            ->and($relation->getLocalKeyName())->toBe('code');
    });
});

describe('Language model', function () {
    it('uses string as primary key', function () {
        $language = new Language;

        expect($language->getKeyName())->toBe('code')
            ->and($language->getKeyType())->toBe('string')
            ->and($language->getIncrementing())->toBeFalse();
    });
});

describe('Region relationships', function () {
    it('has hasMany relationship with Subregions', function () {
        $region = new Region;

        expect($region->subregions())->toBeInstanceOf(HasMany::class);
    });

    it('has hasMany relationship with Countries', function () {
        $region = new Region;

        expect($region->countries())->toBeInstanceOf(HasMany::class);
    });
});

describe('Subregion relationships', function () {
    it('has hasMany relationship with Countries', function () {
        $subregion = new Subregion;

        expect($subregion->countries())->toBeInstanceOf(HasMany::class);
    });

    it('has belongsTo relationship with Region', function () {
        $subregion = new Subregion;

        expect($subregion->region())->toBeInstanceOf(BelongsTo::class);
    });

    it('uses correct FK config for region()', function () {
        $relation = (new Subregion)->region();

        expect($relation->getForeignKeyName())->toBe('region_id');
    });
});

describe('State relationships', function () {
    it('has belongsTo relationship with Country', function () {
        $state = new State;

        expect($state->country())->toBeInstanceOf(BelongsTo::class);
    });

    it('has hasMany relationship with Cities', function () {
        $state = new State;

        expect($state->cities())->toBeInstanceOf(HasMany::class);
    });

    it('uses correct FK config for country()', function () {
        $relation = (new State)->country();

        expect($relation->getForeignKeyName())->toBe('country_id');
    });
});

describe('City relationships', function () {
    it('has belongsTo relationship with Country', function () {
        $city = new City;

        expect($city->country())->toBeInstanceOf(BelongsTo::class);
    });

    it('has belongsTo relationship with State', function () {
        $city = new City;

        expect($city->state())->toBeInstanceOf(BelongsTo::class);
    });

    it('uses correct FK config for country()', function () {
        $relation = (new City)->country();

        expect($relation->getForeignKeyName())->toBe('country_id');
    });

    it('uses correct FK config for state()', function () {
        $relation = (new City)->state();

        expect($relation->getForeignKeyName())->toBe('state_id');
    });
});

describe('Removed v1 methods', function () {
    it('Country does not have regions() plural method', function () {
        expect(method_exists(Country::class, 'regions'))->toBeFalse();
    });

    it('Country does not have subregions() plural method', function () {
        expect(method_exists(Country::class, 'subregions'))->toBeFalse();
    });

    it('Currency does not have country() singular method', function () {
        expect(method_exists(Currency::class, 'country'))->toBeFalse();
    });
});
