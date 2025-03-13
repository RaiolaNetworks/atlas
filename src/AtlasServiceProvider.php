<?php

declare(strict_types=1);

namespace Raiolanetworks\Atlas;

use Illuminate\Support\ServiceProvider;
use Raiolanetworks\Atlas\Commands\CitiesSeeder;
use Raiolanetworks\Atlas\Commands\CountriesSeeder;
use Raiolanetworks\Atlas\Commands\CurrenciesSeeder;
use Raiolanetworks\Atlas\Commands\Install;
use Raiolanetworks\Atlas\Commands\LanguagesSeeder;
use Raiolanetworks\Atlas\Commands\RegionsSeeder;
use Raiolanetworks\Atlas\Commands\StatesSeeder;
use Raiolanetworks\Atlas\Commands\SubregionsSeeder;
use Raiolanetworks\Atlas\Commands\TimezonesSeeder;
use Raiolanetworks\Atlas\Commands\Update;

class AtlasServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/atlas.php', 'atlas');

        // Register the main class to use with the facade
        $this->app->singleton('atlas', fn () => $this);
    }

    /**
     * Boot services.
     */
    public function boot(): void
    {
        // Load translations
        // $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'atlas');

        if ($this->app->runningInConsole()) {
            $this->publishResources();
            $this->loadCommands();
        }
    }

    /**
     * Method to load the migrations when php migrate is run in the console.
     */
    public function loadMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    /**
     * Method to publish the resource to the app resources folder
     */
    private function publishResources(): void
    {
        $this->publishes([
            __DIR__ . '/../config/atlas.php' => config_path('atlas.php'),
        ], 'atlas-config');

        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'atlas-migrations');

        $this->publishes([
            __DIR__ . '/../resources/json' => resource_path('json'),
        ], 'atlas-jsons');

        // $this->publishes([
        //     __DIR__ . '/../resources/lang' => resource_path('lang/vendor/atlas'),
        // ], 'atlas');
    }

    /**
     * Method to publish the resource to the app resources folder
     */
    private function loadCommands(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            Install::class,
            RegionsSeeder::class,
            SubregionsSeeder::class,
            CountriesSeeder::class,
            StatesSeeder::class,
            CitiesSeeder::class,
            TimezonesSeeder::class,
            CurrenciesSeeder::class,
            LanguagesSeeder::class,
            Update::class,
        ]);
    }
}
