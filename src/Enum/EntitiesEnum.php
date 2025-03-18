<?php

declare(strict_types=1);

namespace Raiolanetworks\Atlas\Enum;

enum EntitiesEnum: string
{
    case Languages  = 'languages';
    case Currencies = 'currencies';
    case Regions    = 'regions';
    case Subregions = 'subregions';
    case Countries  = 'countries';
    case States     = 'states';
    case Cities     = 'cities';
    case Timezones  = 'timezones';

    public function getLabel(): ?string
    {
        return $this->name;
    }

    public function getSingular(): ?string
    {
        return match ($this->getLabel()) {
            self::Languages->value  => 'language',
            self::Currencies->value => 'currency',
            self::Regions->value    => 'region',
            self::Subregions->value => 'subregion',
            self::Countries->value  => 'country',
            self::States->value     => 'state',
            self::Cities->value     => 'city',
            self::Timezones->value  => 'timezone',
            default                 => $this->value
        };
    }

    public function isEnabled(): bool
    {
        return match ($this) {
            self::Languages  => config()->boolean('atlas.entities.languages'),
            self::Currencies => config()->boolean('atlas.entities.currencies'),
            self::Regions    => config()->boolean('atlas.entities.regions'),
            self::Subregions => config()->boolean('atlas.entities.subregions'),
            self::Countries  => config()->boolean('atlas.entities.countries'),
            self::States     => config()->boolean('atlas.entities.states'),
            self::Cities     => config()->boolean('atlas.entities.cities'),
            self::Timezones  => config()->boolean('atlas.entities.timezones')
        };
    }
}
