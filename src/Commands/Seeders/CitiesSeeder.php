<?php

declare(strict_types=1);

namespace Raiolanetworks\Atlas\Commands\Seeders;

use Raiolanetworks\Atlas\Enum\EntitiesEnum;
use Raiolanetworks\Atlas\Models\City;

class CitiesSeeder extends BaseSeeder
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    public $signature = 'atlas:cities';

    /**
     * The console command description.
     *
     * @var string
     */
    public $description = 'Seeding of cities in the database';

    protected string $resourceKey = 'cities';

    protected string $pluralName = '';

    protected string $model = City::class;

    public function __construct()
    {
        parent::__construct();

        $this->pluralName = EntitiesEnum::Cities->value;
    }
}
