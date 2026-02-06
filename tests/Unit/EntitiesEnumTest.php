<?php

declare(strict_types=1);

use Raiolanetworks\Atlas\Enum\EntitiesEnum;

it('returns true when entity is enabled in config', function () {
    config()->set('atlas.entities.languages', true);

    expect(EntitiesEnum::Languages->isEnabled())->toBeTrue();
});

it('returns false when entity is disabled in config', function () {
    config()->set('atlas.entities.languages', false);

    expect(EntitiesEnum::Languages->isEnabled())->toBeFalse();
});

it('respects config for each entity type', function (EntitiesEnum $entity) {
    config()->set('atlas.entities.' . $entity->value, true);
    expect($entity->isEnabled())->toBeTrue();

    config()->set('atlas.entities.' . $entity->value, false);
    expect($entity->isEnabled())->toBeFalse();
})->with(EntitiesEnum::cases());

describe('requiredDependencies', function () {
    it('States requires Countries', function () {
        expect(EntitiesEnum::States->requiredDependencies())->toBe([EntitiesEnum::Countries]);
    });

    it('Cities requires States and Countries', function () {
        expect(EntitiesEnum::Cities->requiredDependencies())->toBe([EntitiesEnum::States, EntitiesEnum::Countries]);
    });

    it('Timezones requires Countries', function () {
        expect(EntitiesEnum::Timezones->requiredDependencies())->toBe([EntitiesEnum::Countries]);
    });

    it('returns empty for entities without required dependencies', function (EntitiesEnum $entity) {
        expect($entity->requiredDependencies())->toBe([]);
    })->with([
        EntitiesEnum::Languages,
        EntitiesEnum::Currencies,
        EntitiesEnum::Regions,
        EntitiesEnum::Subregions,
        EntitiesEnum::Countries,
    ]);
});

describe('optionalDependencies', function () {
    it('Countries has Regions, Subregions and Currencies', function () {
        expect(EntitiesEnum::Countries->optionalDependencies())->toBe([
            EntitiesEnum::Regions,
            EntitiesEnum::Subregions,
            EntitiesEnum::Currencies,
        ]);
    });

    it('Subregions has Regions', function () {
        expect(EntitiesEnum::Subregions->optionalDependencies())->toBe([EntitiesEnum::Regions]);
    });

    it('returns empty for entities without optional dependencies', function (EntitiesEnum $entity) {
        expect($entity->optionalDependencies())->toBe([]);
    })->with([
        EntitiesEnum::Languages,
        EntitiesEnum::Currencies,
        EntitiesEnum::Regions,
        EntitiesEnum::States,
        EntitiesEnum::Cities,
        EntitiesEnum::Timezones,
    ]);
});

describe('validateDependencies', function () {
    it('warns when states is enabled but countries is disabled', function () {
        config()->set('atlas.entities.states', true);
        config()->set('atlas.entities.countries', false);
        config()->set('atlas.entities.cities', false);
        config()->set('atlas.entities.timezones', false);

        $warnings = EntitiesEnum::validateDependencies();

        expect($warnings)->toContain("'states' is enabled but its required dependency 'countries' is disabled.");
    });

    it('returns empty array when all dependencies are satisfied', function () {
        foreach (EntitiesEnum::cases() as $entity) {
            config()->set('atlas.entities.' . $entity->value, true);
        }

        expect(EntitiesEnum::validateDependencies())->toBe([]);
    });
});
