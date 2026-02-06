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

    public function handle(): int
    {
        // TODO: consider skipping pivot deletion when called from atlas:update,
        //       or re-seeding only the affected pivot instead of deleting it.
        if (config()->boolean('atlas.entities.timezones')) {
            $this->warn('This will delete all country–timezone pivot data. Run atlas:timezones afterwards to restore it.');
        }

        return parent::handle();
    }

    protected function pivotTables(): array
    {
        if (config()->boolean('atlas.entities.timezones')) {
            return [config()->string('atlas.country_timezone_pivot_tablename')];
        }

        return [];
    }
}
