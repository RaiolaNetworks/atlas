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
