<?php

declare(strict_types=1);

namespace Raiolanetworks\Atlas\Commands\Seeders;

use Raiolanetworks\Atlas\Models\Currency;

class CurrenciesSeeder extends BaseSeeder
{
    public $signature = 'atlas:currencies';

    public $description = 'Seeding of currencies in the database';

    protected string $resourceKey = 'currencies';

    protected string $model = Currency::class;
}
