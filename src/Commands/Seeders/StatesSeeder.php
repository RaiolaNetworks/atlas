<?php

declare(strict_types=1);

namespace Raiolanetworks\Atlas\Commands\Seeders;

use Raiolanetworks\Atlas\Models\State;

class StatesSeeder extends BaseSeeder
{
    public $signature = 'atlas:states';

    public $description = 'Seeding of states in the database';

    protected string $resourceKey = 'states';

    protected string $model = State::class;
}
