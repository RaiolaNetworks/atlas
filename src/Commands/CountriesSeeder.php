<?php

declare(strict_types=1);

namespace Raiolanetworks\Atlas\Commands;

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

    protected string $dataPath = __DIR__ . '/../../resources/json/countries.json';

    protected string $overridedResourcesDataPath = 'json/countries.json';

    protected string $pluralName = '';

    protected string $model = Country::class;

    public function __construct()
    {
        parent::__construct();

        $this->pluralName = EntitiesEnum::Countries->value;
    }

    protected function parseItem(array $rawItem, array &$bulk): void
    {
        $bulk[] = [
            'id'         => $rawItem['id'],
            'iso2'       => $rawItem['iso2'],
            'name'       => $rawItem['name'],
            'phone_code' => $rawItem['phone_code'],
            'currency'   => $rawItem['currency'],
            'iso3'       => $rawItem['iso3'],
            'native'     => $rawItem['native'],
            'region'     => $rawItem['region'],
            'subregion'  => $rawItem['subregion'],
            'latitude'   => $rawItem['latitude'],
            'longitude'  => $rawItem['longitude'],
        ];
    }
}
