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

describe('Region::fromJsonToDBRecord', function () {
    it('maps fields correctly', function () {
        $json = [
            'id'           => 1,
            'name'         => 'Africa',
            'translations' => ['ko' => '아프리카', 'es' => 'África'],
            'wikiDataId'   => 'Q15',
        ];

        $result = Region::fromJsonToDBRecord($json);

        expect($result)
            ->toHaveKey('id', 1)
            ->toHaveKey('name', 'Africa')
            ->toHaveKey('wiki_data_id', 'Q15')
            ->and($result['translations'])->toBe(json_encode($json['translations']));
    });
});

describe('Subregion::fromJsonToDBRecord', function () {
    it('maps fields correctly with regions enabled', function () {
        config()->set('atlas.entities.regions', true);

        $json = [
            'id'           => 19,
            'name'         => 'Australia and New Zealand',
            'region_id'    => 5,
            'translations' => ['ko' => '오스트랄라시아'],
            'wikiDataId'   => 'Q45256',
        ];

        $result = Subregion::fromJsonToDBRecord($json);

        expect($result)->toHaveKeys(['id', 'name', 'region_id', 'translations', 'wiki_data_id'])
            ->and($result['region_id'])->toBe(5);
    });

    it('excludes region_id when regions disabled', function () {
        config()->set('atlas.entities.regions', false);

        $json = [
            'id'           => 19,
            'name'         => 'Australia and New Zealand',
            'region_id'    => 5,
            'translations' => ['ko' => '오스트랄라시아'],
            'wikiDataId'   => 'Q45256',
        ];

        $result = Subregion::fromJsonToDBRecord($json);

        expect($result)->not->toHaveKey('region_id');
    });
});

describe('Country::fromJsonToDBRecord', function () {
    beforeEach(function () {
        config()->set('atlas.entities.regions', true);
        config()->set('atlas.entities.subregions', true);
        config()->set('atlas.entities.currencies', true);
    });

    it('maps all fields correctly when all entities enabled', function () {
        $json = [
            'name'         => 'Afghanistan',
            'iso2'         => 'AF',
            'iso3'         => 'AFG',
            'numeric_code' => '004',
            'phonecode'    => '93',
            'capital'      => 'Kabul',
            'currency'     => 'AFN',
            'tld'          => '.af',
            'native'       => 'افغانستان',
            'region'       => 'Asia',
            'region_id'    => 3,
            'subregion'    => 'Southern Asia',
            'subregion_id' => 14,
            'nationality'  => 'Afghan',
            'translations' => ['ko' => '아프가니스탄'],
            'latitude'     => '33.00000000',
            'longitude'    => '65.00000000',
            'emoji'        => '🇦🇫',
            'emojiU'       => 'U+1F1E6 U+1F1EB',
        ];

        $result = Country::fromJsonToDBRecord($json);

        expect($result)
            ->toHaveKey('region_id', 3)
            ->toHaveKey('subregion_id', 14)
            ->toHaveKey('currency_code', 'AFN')
            ->toHaveKey('name', 'Afghanistan')
            ->toHaveKey('iso2', 'AF');
    });

    it('excludes region_id when regions disabled', function () {
        config()->set('atlas.entities.regions', false);

        $json = [
            'name'         => 'Afghanistan',
            'iso2'         => 'AF',
            'iso3'         => 'AFG',
            'numeric_code' => '004',
            'phonecode'    => '93',
            'capital'      => 'Kabul',
            'currency'     => 'AFN',
            'tld'          => '.af',
            'native'       => 'افغانستان',
            'region'       => 'Asia',
            'region_id'    => 3,
            'subregion'    => 'Southern Asia',
            'subregion_id' => 14,
            'nationality'  => 'Afghan',
            'translations' => ['ko' => '아프가니스탄'],
            'latitude'     => '33.00000000',
            'longitude'    => '65.00000000',
            'emoji'        => '🇦🇫',
            'emojiU'       => 'U+1F1E6 U+1F1EB',
        ];

        $result = Country::fromJsonToDBRecord($json);

        expect($result)->not->toHaveKey('region_id');
    });

    it('excludes subregion_id when subregions disabled', function () {
        config()->set('atlas.entities.subregions', false);

        $json = [
            'name'         => 'Afghanistan',
            'iso2'         => 'AF',
            'iso3'         => 'AFG',
            'numeric_code' => '004',
            'phonecode'    => '93',
            'capital'      => 'Kabul',
            'currency'     => 'AFN',
            'tld'          => '.af',
            'native'       => 'افغانستان',
            'region'       => 'Asia',
            'region_id'    => 3,
            'subregion'    => 'Southern Asia',
            'subregion_id' => 14,
            'nationality'  => 'Afghan',
            'translations' => ['ko' => '아프가니스탄'],
            'latitude'     => '33.00000000',
            'longitude'    => '65.00000000',
            'emoji'        => '🇦🇫',
            'emojiU'       => 'U+1F1E6 U+1F1EB',
        ];

        $result = Country::fromJsonToDBRecord($json);

        expect($result)->not->toHaveKey('subregion_id');
    });

    it('excludes currency_code when currencies disabled', function () {
        config()->set('atlas.entities.currencies', false);

        $json = [
            'name'         => 'Afghanistan',
            'iso2'         => 'AF',
            'iso3'         => 'AFG',
            'numeric_code' => '004',
            'phonecode'    => '93',
            'capital'      => 'Kabul',
            'currency'     => 'AFN',
            'tld'          => '.af',
            'native'       => 'افغانستان',
            'region'       => 'Asia',
            'region_id'    => 3,
            'subregion'    => 'Southern Asia',
            'subregion_id' => 14,
            'nationality'  => 'Afghan',
            'translations' => ['ko' => '아프가니스탄'],
            'latitude'     => '33.00000000',
            'longitude'    => '65.00000000',
            'emoji'        => '🇦🇫',
            'emojiU'       => 'U+1F1E6 U+1F1EB',
        ];

        $result = Country::fromJsonToDBRecord($json);

        expect($result)->not->toHaveKey('currency_code');
    });
});

describe('State::fromJsonToDBRecord', function () {
    it('maps fields correctly with countries enabled', function () {
        config()->set('atlas.entities.countries', true);

        $json = [
            'id'           => 1,
            'name'         => 'Badakhshan',
            'country_id'   => 1,
            'country_code' => 'AF',
            'country_name' => 'Afghanistan',
            'state_code'   => 'BDS',
            'type'         => 'province',
            'latitude'     => '36.73477250',
            'longitude'    => '70.81199530',
        ];

        $result = State::fromJsonToDBRecord($json);

        expect($result)
            ->toHaveKey('country_id', 1)
            ->toHaveKey('name', 'Badakhshan');
    });

    it('excludes country_id when countries disabled', function () {
        config()->set('atlas.entities.countries', false);

        $json = [
            'id'           => 1,
            'name'         => 'Badakhshan',
            'country_id'   => 1,
            'country_code' => 'AF',
            'country_name' => 'Afghanistan',
            'state_code'   => 'BDS',
            'type'         => 'province',
            'latitude'     => '36.73477250',
            'longitude'    => '70.81199530',
        ];

        $result = State::fromJsonToDBRecord($json);

        expect($result)->not->toHaveKey('country_id');
    });
});

describe('City::fromJsonToDBRecord', function () {
    beforeEach(function () {
        config()->set('atlas.entities.states', true);
        config()->set('atlas.entities.countries', true);
    });

    it('maps fields correctly with all entities enabled', function () {
        $json = [
            'id'           => 1,
            'name'         => 'Kabul',
            'state_id'     => 1,
            'state_code'   => 'KBL',
            'state_name'   => 'Kabul',
            'country_id'   => 1,
            'country_code' => 'AF',
            'country_name' => 'Afghanistan',
            'latitude'     => '34.52813000',
            'longitude'    => '69.17233000',
            'wikiDataId'   => 'Q5838',
        ];

        $result = City::fromJsonToDBRecord($json);

        expect($result)
            ->toHaveKey('state_id', 1)
            ->toHaveKey('country_id', 1)
            ->toHaveKey('wiki_data_id', 'Q5838');
    });

    it('excludes state_id when states disabled', function () {
        config()->set('atlas.entities.states', false);

        $json = [
            'id'           => 1,
            'name'         => 'Kabul',
            'state_id'     => 1,
            'state_code'   => 'KBL',
            'state_name'   => 'Kabul',
            'country_id'   => 1,
            'country_code' => 'AF',
            'country_name' => 'Afghanistan',
            'latitude'     => '34.52813000',
            'longitude'    => '69.17233000',
            'wikiDataId'   => 'Q5838',
        ];

        $result = City::fromJsonToDBRecord($json);

        expect($result)->not->toHaveKey('state_id');
    });

    it('excludes country_id when countries disabled', function () {
        config()->set('atlas.entities.countries', false);

        $json = [
            'id'           => 1,
            'name'         => 'Kabul',
            'state_id'     => 1,
            'state_code'   => 'KBL',
            'state_name'   => 'Kabul',
            'country_id'   => 1,
            'country_code' => 'AF',
            'country_name' => 'Afghanistan',
            'latitude'     => '34.52813000',
            'longitude'    => '69.17233000',
            'wikiDataId'   => 'Q5838',
        ];

        $result = City::fromJsonToDBRecord($json);

        expect($result)->not->toHaveKey('country_id');
    });
});

describe('Timezone::fromJsonToDBRecord', function () {
    it('maps zoneName to zone_name', function () {
        $json = [
            'zoneName'      => 'Asia/Kabul',
            'gmtOffset'     => 16200,
            'gmtOffsetName' => 'UTC+04:30',
            'tzName'        => 'Afghanistan Time',
        ];

        $result = Timezone::fromJsonToDBRecord($json);

        expect($result)->toBe([
            'zone_name'       => 'Asia/Kabul',
            'gmt_offset'      => 16200,
            'gmt_offset_name' => 'UTC+04:30',
            'tz_name'         => 'Afghanistan Time',
        ]);
    });
});

describe('Currency::fromJsonToDBRecord', function () {
    it('maps fields directly', function () {
        $json = [
            'code'           => 'USD',
            'name'           => 'US Dollar',
            'symbol'         => '$',
            'symbol_native'  => '$',
            'decimal_digits' => 2,
        ];

        $result = Currency::fromJsonToDBRecord($json);

        expect($result)->toBe([
            'code'           => 'USD',
            'name'           => 'US Dollar',
            'symbol'         => '$',
            'symbol_native'  => '$',
            'decimal_digits' => 2,
        ]);
    });
});

describe('Language::fromJsonToDBRecord', function () {
    it('maps fields directly', function () {
        $json = [
            'code'        => 'en',
            'name'        => 'English',
            'name_native' => 'English',
            'dir'         => 'ltr',
        ];

        $result = Language::fromJsonToDBRecord($json);

        expect($result)->toBe([
            'code'        => 'en',
            'name'        => 'English',
            'name_native' => 'English',
            'dir'         => 'ltr',
        ]);
    });
});
