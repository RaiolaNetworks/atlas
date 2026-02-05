<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Raiolanetworks\Atlas\Models\Country;
use Raiolanetworks\Atlas\Models\Currency;
use Raiolanetworks\Atlas\Models\Region;
use Raiolanetworks\Atlas\Models\State;
use Raiolanetworks\Atlas\Models\Subregion;
use Raiolanetworks\Atlas\Models\Timezone;

describe('Country relationships', function () {
    it('has belongsTo relationship with Region', function () {
        $country = new Country;

        expect($country->regions())->toBeInstanceOf(BelongsTo::class);
    });

    it('has belongsTo relationship with Subregion', function () {
        $country = new Country;

        expect($country->subregions())->toBeInstanceOf(BelongsTo::class);
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
});

describe('Timezone relationships', function () {
    it('has belongsToMany relationship with Countries', function () {
        $timezone = new Timezone;

        expect($timezone->countries())->toBeInstanceOf(BelongsToMany::class);
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
});
