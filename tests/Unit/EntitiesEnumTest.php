<?php

declare(strict_types=1);

use Raiolanetworks\Atlas\Enum\EntitiesEnum;

it('returns correct singular form for each entity', function (EntitiesEnum $entity, string $expectedSingular) {
    expect($entity->getSingular())->toBe($expectedSingular);
})->with([
    [EntitiesEnum::Languages, 'language'],
    [EntitiesEnum::Currencies, 'currency'],
    [EntitiesEnum::Regions, 'region'],
    [EntitiesEnum::Subregions, 'subregion'],
    [EntitiesEnum::Countries, 'country'],
    [EntitiesEnum::States, 'state'],
    [EntitiesEnum::Cities, 'city'],
    [EntitiesEnum::Timezones, 'timezone'],
]);

it('returns the name as label', function (EntitiesEnum $entity) {
    expect($entity->getLabel())->toBe($entity->name);
})->with(EntitiesEnum::cases());

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
