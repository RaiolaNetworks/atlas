<?php

declare(strict_types=1);

namespace Raiolanetworks\Atlas\Commands;

use Raiolanetworks\Atlas\Enum\EntitiesEnum;
use Raiolanetworks\Atlas\Models\Subregion;

class SubregionsSeeder extends BaseSeeder
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    public $signature = 'atlas:subregions';

    /**
     * The console command description.
     *
     * @var string
     */
    public $description = 'Seeding of subregions in the database';

    protected string $resourceKey = 'subregions';

    protected string $pluralName = '';

    protected string $model = Subregion::class;

    public function __construct()
    {
        parent::__construct();

        $this->pluralName = EntitiesEnum::Subregions->value;
    }
}
