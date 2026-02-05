<?php

declare(strict_types=1);

namespace Raiolanetworks\Atlas\Commands\Seeders;

use Iterator;
use Raiolanetworks\Atlas\Enum\EntitiesEnum;
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

    protected string $resourceKey = 'countries';

    protected string $pluralName = '';

    protected string $model = Timezone::class;

    protected string $insertionMode = self::INDIVIDUAL_INSERTION_MODE;

    /**
     * Data of the data file
     *
     * @var array<int, array{
     *      id: int,
     *      timezones: array<int, array<string, string|int>>
     * }>
     */
    protected array $data;

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

        /** @var string $jsonData */
        $jsonData = file_get_contents($this->dataPath);
        $data     = json_decode($jsonData, true);
        unset($jsonData);

        if (! is_array($data)) {
            $this->error('The json data is incorrect...');

            return false;
        }

        $this->data = $data;

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

    protected function whenRecordInserted(Timezone $instance): void
    {
        foreach ($this->data as $rawCountry) {
            foreach ($rawCountry['timezones'] as $rawTimezone) {
                if ($rawTimezone['zoneName'] === $instance->zone_name) {
                    $instance->countries()->attach($rawCountry['id']);

                    continue 2;
                }
            }
        }
    }
}
