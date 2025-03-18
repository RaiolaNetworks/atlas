<?php

declare(strict_types=1);

namespace Raiolanetworks\Atlas\Commands\Seeders;

use Illuminate\Console\Command;
use Raiolanetworks\Atlas\Enum\EntitiesEnum;
use Raiolanetworks\Atlas\Models\Country;

class CountriesSeeder extends BaseSeeder
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    public $signature = 'atlas:countries';

    /**
     * The console command description.
     *
     * @var string
     */
    public $description = 'Seeding of countries in the database';

    protected string $resourceKey = 'countries';

    protected string $pluralName = '';

    protected string $model = Country::class;

    protected string $insertionMode = self::INDIVIDUAL_INSERTION_MODE;

    public function __construct()
    {
        parent::__construct();

        $this->pluralName = EntitiesEnum::Countries->value;
    }
}
