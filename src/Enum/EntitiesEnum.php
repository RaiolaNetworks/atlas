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

    public function getLabel(): string
    {
        return $this->name;
    }

    public function getSingular(): string
    {
        return match ($this) {
            self::Languages  => 'language',
            self::Currencies => 'currency',
            self::Regions    => 'region',
            self::Subregions => 'subregion',
            self::Countries  => 'country',
            self::States     => 'state',
            self::Cities     => 'city',
            self::Timezones  => 'timezone',
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

    /**
     * @return list<self>
     */
    public function dependencies(): array
    {
        return match ($this) {
            self::Subregions => [self::Regions],
            self::Countries  => [self::Regions, self::Subregions, self::Currencies],
            self::States     => [self::Countries],
            self::Cities     => [self::States, self::Countries],
            default          => [],
        };
    }

    /**
     * @return list<string>
     */
    public static function validateDependencies(): array
    {
        $warnings = [];

        foreach (self::cases() as $entity) {
            if (! $entity->isEnabled()) {
                continue;
            }

            foreach ($entity->dependencies() as $dependency) {
                if (! $dependency->isEnabled()) {
                    $warnings[] = "'{$entity->value}' is enabled but its dependency '{$dependency->value}' is disabled.";
                }
            }
        }

        return $warnings;
    }
}
