<?php

declare(strict_types=1);

use Raiolanetworks\Atlas\Models\City;
use Raiolanetworks\Atlas\Models\Country;
use Raiolanetworks\Atlas\Models\Currency;
use Raiolanetworks\Atlas\Models\Language;
use Raiolanetworks\Atlas\Models\Region;
use Raiolanetworks\Atlas\Models\State;
use Raiolanetworks\Atlas\Models\Subregion;
use Raiolanetworks\Atlas\Models\Timezone;

describe('Translations cast', function () {
    it('casts translations to array on Country model', function () {
        Country::query()->insert([
            'id'           => 1,
            'name'         => 'Test',
            'iso2'         => 'TS',
            'iso3'         => 'TST',
            'numeric_code' => '999',
            'phonecode'    => '99',
            'tld'          => '.ts',
            'region_name'  => 'Test Region',
            'nationality'  => 'Tester',
            'translations' => json_encode(['en' => 'Test']),
            'latitude'     => '0',
            'longitude'    => '0',
            'emoji'        => '',
            'emojiU'       => '',
        ]);

        $country = Country::query()->find(1);

        expect($country->translations)->toBeArray();
    });
});

describe('Model fillable configuration', function () {
    it('uses $fillable (not $guarded) on all models', function (string $modelClass) {
        /** @var Illuminate\Database\Eloquent\Model $model */
        $model = new $modelClass;

        expect($model->getFillable())->not->toBeEmpty();
        expect($model->getGuarded())->toBe(['*']);
    })->with([
        Country::class,
        Currency::class,
        Language::class,
        Region::class,
        Subregion::class,
        State::class,
        City::class,
        Timezone::class,
    ]);
});

describe('Pivot table config', function () {
    it('Country::timezones() uses configured pivot table name', function () {
        $customName = 'custom_country_tz_pivot';
        config()->set('atlas.country_timezone_pivot_tablename', $customName);

        $relation = (new Country)->timezones();

        expect($relation->getTable())->toBe($customName);
    });
});
