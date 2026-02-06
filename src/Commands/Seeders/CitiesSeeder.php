<?php

declare(strict_types=1);

namespace Raiolanetworks\Atlas\Commands\Seeders;

use Raiolanetworks\Atlas\Models\City;

class CitiesSeeder extends BaseSeeder
{
    public $signature = 'atlas:cities';

    public $description = 'Seeding of cities in the database';

    protected string $resourceKey = 'cities';

    protected string $model = City::class;
}
