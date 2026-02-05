<?php

declare(strict_types=1);

namespace Raiolanetworks\Atlas\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Raiolanetworks\Atlas\AtlasServiceProvider;

class DisabledEntitiesTestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            AtlasServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('atlas.entities.languages', true);
        $app['config']->set('atlas.entities.currencies', true);
        $app['config']->set('atlas.entities.regions', false);
        $app['config']->set('atlas.entities.subregions', false);
        $app['config']->set('atlas.entities.countries', true);
        $app['config']->set('atlas.entities.states', false);
        $app['config']->set('atlas.entities.cities', false);
        $app['config']->set('atlas.entities.timezones', true);

        $app['config']->set('atlas.languages_tablename', 'languages');
        $app['config']->set('atlas.currencies_tablename', 'currencies');
        $app['config']->set('atlas.regions_tablename', 'regions');
        $app['config']->set('atlas.subregions_tablename', 'subregions');
        $app['config']->set('atlas.countries_tablename', 'countries');
        $app['config']->set('atlas.states_tablename', 'states');
        $app['config']->set('atlas.cities_tablename', 'cities');
        $app['config']->set('atlas.timezones_tablename', 'timezones');
        $app['config']->set('atlas.country_timezone_pivot_tablename', 'country_timezone');
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}
