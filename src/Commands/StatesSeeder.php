<?php

declare(strict_types=1);

namespace Raiolanetworks\Atlas\Commands;

use Illuminate\Console\Command;
use Raiolanetworks\Atlas\Enum\EntitiesEnum;
use Raiolanetworks\Atlas\Models\State;

class StatesSeeder extends BaseSeeder
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    public $signature = 'atlas:states';

    /**
     * The console command description.
     *
     * @var string
     */
    public $description = 'Seeding of states in the database';

    protected string $resourceKey = 'states';

    protected string $pluralName = '';

    protected string $model = State::class;

    public function __construct()
    {
        parent::__construct();

        $this->pluralName = EntitiesEnum::States->value;
    }
}
