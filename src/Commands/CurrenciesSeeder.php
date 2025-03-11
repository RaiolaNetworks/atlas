<?php

declare(strict_types=1);

namespace Raiolanetworks\Atlas\Commands;

use Illuminate\Console\Command;
use Raiolanetworks\Atlas\Enum\EntitiesEnum;
use Raiolanetworks\Atlas\Models\Country;
use Raiolanetworks\Atlas\Models\Currency;

class CurrenciesSeeder extends BaseSeeder
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    public $signature = 'atlas:currencies';

    /**
     * The console command description.
     *
     * @var string
     */
    public $description = 'Seeding of currencies in the database';

    protected string $dataPath = __DIR__ . '/../../resources/json/currencies.json';

    protected string $overridedResourcesDataPath = 'json/currencies.json';

    protected string $pluralName = '';

    protected string $model = Currency::class;

    public function __construct()
    {
        parent::__construct();

        $this->pluralName = EntitiesEnum::Currencies->value;
    }

    protected function parseItem(array $rawItem, array &$bulk): void
    {
        $country = Country::whereCurrency($rawItem['code'])->first();

        $bulk[] = [
            'country_id'     => $country ? $country->id : null,
            'name'           => $rawItem['name'],
            'code'           => $rawItem['code'],
            'symbol'         => $rawItem['symbol'],
            'symbol_native'  => $rawItem['symbol_native'],
            'decimal_digits' => $rawItem['decimal_digits'],

        ];
    }
}
