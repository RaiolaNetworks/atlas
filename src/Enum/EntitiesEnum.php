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
     * Hard dependencies — the entity cannot function without these.
     *
     * @return list<self>
     */
    public function requiredDependencies(): array
    {
        return match ($this) {
            self::States    => [self::Countries],
            self::Cities    => [self::States, self::Countries],
            self::Timezones => [self::Countries],
            default         => [],
        };
    }

    /**
     * Config-conditional dependencies — the seeder populates these FK
     * columns only when the dependency entity is enabled in config.
     * If the dependency is enabled but not seeded, dangling FKs result.
     *
     * @return list<self>
     */
    public function optionalDependencies(): array
    {
        return match ($this) {
            self::Countries  => [self::Regions, self::Subregions, self::Currencies],
            self::Subregions => [self::Regions],
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

            foreach ($entity->requiredDependencies() as $dependency) {
                if (! $dependency->isEnabled()) {
                    $warnings[] = "'{$entity->value}' is enabled but its required dependency '{$dependency->value}' is disabled.";
                }
            }
        }

        return $warnings;
    }
}
