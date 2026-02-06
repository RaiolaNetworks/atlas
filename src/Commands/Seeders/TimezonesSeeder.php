<?php

declare(strict_types=1);

namespace Raiolanetworks\Atlas\Commands\Seeders;

use Iterator;
use JsonMachine\Items;
use JsonMachine\JsonDecoder\ExtJsonDecoder;
use Raiolanetworks\Atlas\Models\BaseModel;
use Raiolanetworks\Atlas\Models\Timezone;

class TimezonesSeeder extends BaseSeeder
{
    public $signature = 'atlas:timezones';

    public $description = 'Seeding of timezones in the database';

    protected string $resourceKey = 'timezones';

    protected string $model = Timezone::class;

    protected string $insertionMode = self::INDIVIDUAL_INSERTION_MODE;

    /**
     * @var array<string, list<int>>
     */
    private array $timezoneCountries = [];

    /**
     * Zone names already inserted during this seeding run.
     *
     * @var array<string, true>
     */
    private array $insertedZoneNames = [];

    protected function pivotTables(): array
    {
        return [config()->string('atlas.country_timezone_pivot_tablename')];
    }

    protected function checkDataFile(): bool
    {
        if (! parent::checkDataFile()) {
            return false;
        }

        $this->timezoneCountries = [];
        $this->insertedZoneNames = [];

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
            $zoneName = $timezone['zoneName'];

            if (isset($this->insertedZoneNames[$zoneName])) {
                continue;
            }

            $this->insertedZoneNames[$zoneName] = true;

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
