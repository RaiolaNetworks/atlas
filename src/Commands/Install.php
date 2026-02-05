<?php

declare(strict_types=1);

namespace Raiolanetworks\Atlas\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

use function Laravel\Prompts\multiselect;

use Raiolanetworks\Atlas\Enum\EntitiesEnum;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    public $signature = 'atlas:install';

    /**
     * The console command description.
     *
     * @var string
     */
    public $description = 'Install all the data';

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
        // Validate entity dependencies
        foreach (EntitiesEnum::validateDependencies() as $warning) {
            $this->warn($warning);
        }

        // Load migrations in migrations queue and run
        app()->make('atlas')->loadMigrations();
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

        // Call necessary seeders
        foreach ($choice as $value) {
            $lower   = Str::lower(strval($value));
            $command = 'atlas:' . EntitiesEnum::from($lower)->value;

            $this->newLine();
            $this->line('Seeding ' . $lower . '...');

            $exitCode = $this->call($command);

            if ($exitCode !== self::SUCCESS) {
                $this->error("Seeder for {$lower} failed.");

                return self::FAILURE;
            }
        }

        $this->newLine();
        $this->info('Successful seeding.');

        return self::SUCCESS;
    }
}
