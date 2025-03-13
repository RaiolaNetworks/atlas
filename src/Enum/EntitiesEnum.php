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
            self::Regions->value    => 'region',
            self::Subregions->value => 'subregion',
            self::Countries->value  => 'country',
            self::States->value     => 'state',
            self::Cities->value     => 'city',
            self::Timezones->value  => 'timezone',
            self::Currencies->value => 'currency',
            self::Languages->value  => 'language',
            default                 => $this->value
        };
    }
}
