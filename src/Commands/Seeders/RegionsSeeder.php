<?php

declare(strict_types=1);

namespace Raiolanetworks\Atlas\Commands\Seeders;

use Raiolanetworks\Atlas\Models\Region;

class RegionsSeeder extends BaseSeeder
{
    public $signature = 'atlas:regions';

    public $description = 'Seeding of regions in the database';

    protected string $resourceKey = 'regions';

    protected string $model = Region::class;
}
