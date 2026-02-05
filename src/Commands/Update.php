<?php

declare(strict_types=1);

namespace Raiolanetworks\Atlas\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Raiolanetworks\Atlas\Enum\EntitiesEnum;

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
    public $description = 'Re-seed all enabled entities with the latest data from JSON files';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $enabledEntities = array_filter(EntitiesEnum::cases(), fn (EntitiesEnum $entity) => $entity->isEnabled());

        if (! $this->checkRequiredTables($enabledEntities)) {
            return self::FAILURE;
        }

        foreach ($enabledEntities as $entity) {
            $this->newLine();
            $this->line('Seeding ' . $entity->value . '...');
            $this->call('atlas:' . $entity->value);
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
