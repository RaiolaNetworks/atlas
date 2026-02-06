<?php

declare(strict_types=1);

namespace Raiolanetworks\Atlas\Commands;

use Illuminate\Console\Command;

use function Laravel\Prompts\multiselect;

use Raiolanetworks\Atlas\Commands\Concerns\ValidatesDependencies;
use Raiolanetworks\Atlas\Enum\EntitiesEnum;

class Install extends Command
{
    use ValidatesDependencies;

    public $signature = 'atlas:install';

    public $description = 'Install all the data';

    public function handle(): int
    {
        $this->warnAboutDisabledDependencies();

        $this->call('migrate');

        // Select the seeders to be executed
        $entities = array_filter(EntitiesEnum::cases(), fn (EntitiesEnum $entity) => $entity->isEnabled());
        $options  = array_column($entities, 'name');
        $choice   = multiselect(
            label: 'Which seeders do you want to run? (default: all)',
            options: $options,
            default: $options,
            scroll: 6
        );

        // Filter by selection, preserving EntitiesEnum::cases() definition order
        $selected = array_filter(
            EntitiesEnum::cases(),
            fn (EntitiesEnum $entity) => in_array($entity->name, $choice),
        );

        // Validate that required dependencies are included in the selection
        foreach ($selected as $entity) {
            foreach ($entity->requiredDependencies() as $dependency) {
                if (! in_array($dependency, $selected)) {
                    $this->error("'{$entity->value}' requires '{$dependency->value}' to be selected.");

                    return self::FAILURE;
                }
            }

            foreach ($entity->optionalDependencies() as $dependency) {
                if ($dependency->isEnabled() && ! in_array($dependency, $selected)) {
                    $this->error("'{$entity->value}' populates '{$dependency->value}' foreign keys (enabled in config) — you must also select '{$dependency->value}' or disable it in config.");

                    return self::FAILURE;
                }
            }
        }

        foreach ($selected as $entity) {
            $this->newLine();
            $this->line('Seeding ' . $entity->value . '...');

            $exitCode = $this->call('atlas:' . $entity->value);

            if ($exitCode !== self::SUCCESS) {
                $this->error("Seeder for {$entity->value} failed.");

                return self::FAILURE;
            }
        }

        $this->newLine();
        $this->info('Successful seeding.');

        return self::SUCCESS;
    }
}
