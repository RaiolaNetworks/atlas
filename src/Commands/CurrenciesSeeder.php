<?php

declare(strict_types=1);

namespace Raiolanetworks\Atlas\Commands;

use Illuminate\Console\Command;
use Iterator;
use Raiolanetworks\Atlas\Enum\EntitiesEnum;
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

    protected string $resourceKey = 'currencies';

    protected string $pluralName = '';

    protected string $model = Currency::class;

    public function __construct()
    {
        parent::__construct();

        $this->pluralName = EntitiesEnum::Currencies->value;
    }

    protected function generateElementsOfBulk(array $jsonItem): Iterator
    {
        yield $this->model::fromJsonToDBRecord($jsonItem);
    }
}
