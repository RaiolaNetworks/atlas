<?php

declare(strict_types=1);

namespace Raiolanetworks\Atlas\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Raiolanetworks\Atlas\Commands\Concerns\ValidatesDependencies;
use Raiolanetworks\Atlas\Enum\EntitiesEnum;

class Update extends Command
{
    use ValidatesDependencies;

    public $signature = 'atlas:update';

    public $description = 'Re-seed all enabled entities with the latest data from JSON files';

    public function handle(): int
    {
        $this->warnAboutDisabledDependencies();

        $enabledEntities = array_filter(EntitiesEnum::cases(), fn (EntitiesEnum $entity) => $entity->isEnabled());

        if (! $this->checkRequiredTables($enabledEntities)) {
            return self::FAILURE;
        }

        foreach ($enabledEntities as $entity) {
            $this->newLine();
            $this->line('Seeding ' . $entity->value . '...');

            $exitCode = $this->call('atlas:' . $entity->value);

            if ($exitCode !== self::SUCCESS) {
                $this->error("Seeder for {$entity->value} failed.");

                return self::FAILURE;
            }
        }

        $this->newLine();
        $this->info('All enabled entities have been updated successfully.');

        return self::SUCCESS;
    }

    /**
     * @param EntitiesEnum[] $entities
     */
    private function checkRequiredTables(array $entities): bool
    {
        $this->info('Checking if required tables exist...');

        $missingTables = [];

        foreach ($entities as $entity) {
            $table = config()->string('atlas.' . $entity->value . '_tablename');

            if (! Schema::hasTable($table)) {
                $missingTables[] = $table;
            }
        }

        if (config()->boolean('atlas.entities.timezones') && config()->boolean('atlas.entities.countries')) {
            $pivotTable = config()->string('atlas.country_timezone_pivot_tablename');

            if (! Schema::hasTable($pivotTable)) {
                $missingTables[] = $pivotTable;
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
