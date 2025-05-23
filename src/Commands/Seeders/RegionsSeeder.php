<?php

declare(strict_types=1);

namespace Raiolanetworks\Atlas\Commands\Seeders;

use Raiolanetworks\Atlas\Enum\EntitiesEnum;
use Raiolanetworks\Atlas\Models\Region;

class RegionsSeeder extends BaseSeeder
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    public $signature = 'atlas:regions';

    /**
     * The console command description.
     *
     * @var string
     */
    public $description = 'Seeding of regions in the database';

    protected string $resourceKey = 'regions';

    protected string $pluralName = '';

    protected string $model = Region::class;

    public function __construct()
    {
        parent::__construct();

        $this->pluralName = EntitiesEnum::Regions->value;
    }
}
