<?php

declare(strict_types=1);

namespace Raiolanetworks\Atlas\Commands\Seeders;

use Iterator;
use JsonMachine\Items;
use JsonMachine\JsonDecoder\ExtJsonDecoder;
use Raiolanetworks\Atlas\Enum\EntitiesEnum;
use Raiolanetworks\Atlas\Models\BaseModel;
use Raiolanetworks\Atlas\Models\Timezone;

class TimezonesSeeder extends BaseSeeder
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    public $signature = 'atlas:timezones';

    /**
     * The console command description.
     *
     * @var string
     */
    public $description = 'Seeding of timezones in the database';

    protected string $resourceKey = 'timezones';

    protected string $pluralName = '';

    protected string $model = Timezone::class;

    protected string $insertionMode = self::INDIVIDUAL_INSERTION_MODE;

    /**
     * @var array<string, list<int>>
     */
    private array $timezoneCountries = [];

    public function __construct()
    {
        parent::__construct();
        $this->pluralName = EntitiesEnum::Timezones->value;
    }

    protected function checkDataFile(): bool
    {
        if (! parent::checkDataFile()) {
            return false;
        }

        $items = Items::fromFile($this->dataPath, ['decoder' => new ExtJsonDecoder(true)]);

        foreach ($items as $item) {
            /** @var array{id: int, timezones: array<int, array{zoneName: string}>} $item */
            $countryId = $item['id'];

            foreach ($item['timezones'] as $rawTimezone) {
                $this->timezoneCountries[$rawTimezone['zoneName']][] = $countryId;
            }
        }

        return true;
    }

    /**
     * @param array{
     *    id: int,
     *    timezones: array<array{
     *      zoneName: string,
     *    }>
     * } $jsonItem
     */
    protected function generateElementsOfBulk(array $jsonItem): Iterator
    {
        foreach ($jsonItem['timezones'] as $timezone) {
            $exists = Timezone::query()->where('zone_name', $timezone['zoneName'])->exists();

            if ($exists) {
                continue;
            }

            yield $this->model::fromJsonToDBRecord($timezone);
        }
    }

    protected function whenRecordInserted(BaseModel $instance): void
    {
        assert($instance instanceof Timezone);

        if (isset($this->timezoneCountries[$instance->zone_name])) {
            $instance->countries()->attach($this->timezoneCountries[$instance->zone_name]);
        }
    }
}
