<?php

declare(strict_types=1);

namespace Raiolanetworks\Atlas\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class Update extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    public $signature = 'atlas:update';

    /**
     * The console command description.
     *
     * @var string
     */
    public $description = 'Update all the data comparing with the current data in your database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        ini_set('memory_limit', '-1');

        $this->info('Updating data of countries, cities, currencies, languages, states and timezones');

        if (! $this->checkRequiredTables()) {
            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    private function checkRequiredTables(): bool
    {
        $this->newLine();
        $this->info('Checking if required tables exist...');

        $requiredTables = [
            config()->string('atlas.countries_tablename'),
            config()->string('atlas.states_tablename'),
            config()->string('atlas.cities_tablename'),
            config()->string('atlas.currencies_tablename'),
            config()->string('atlas.languages_tablename'),
            config()->string('atlas.timezones_tablename'),
        ];

        $missingTables = [];

        foreach ($requiredTables as $table) {
            if (! Schema::hasTable($table)) {
                $missingTables[] = $table;
            }
        }

        if (count($missingTables) > 0) {
            $this->error('The following tables are missing: ' . implode(', ', $missingTables));
            $this->error('Please run migrations before updating data.');

            return false;
        }

        $this->info('All required tables exist.');

        $this->newLine();

        return true;
    }
}
