<?php

declare(strict_types=1);

namespace Raiolanetworks\Atlas\Commands\Seeders;

use Raiolanetworks\Atlas\Models\Subregion;

class SubregionsSeeder extends BaseSeeder
{
    public $signature = 'atlas:subregions';

    public $description = 'Seeding of subregions in the database';

    protected string $resourceKey = 'subregions';

    protected string $model = Subregion::class;
}
