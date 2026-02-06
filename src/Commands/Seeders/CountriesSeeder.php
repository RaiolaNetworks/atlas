<?php

declare(strict_types=1);

namespace Raiolanetworks\Atlas\Commands\Seeders;

use Raiolanetworks\Atlas\Models\Country;

class CountriesSeeder extends BaseSeeder
{
    public $signature = 'atlas:countries';

    public $description = 'Seeding of countries in the database';

    protected string $resourceKey = 'countries';

    protected string $model = Country::class;

    protected function pivotTables(): array
    {
        return [config()->string('atlas.country_timezone_pivot_tablename')];
    }
}
