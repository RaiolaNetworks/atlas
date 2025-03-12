<?php

declare(strict_types=1);

namespace Raiolanetworks\Atlas\Commands;

use Illuminate\Console\Command;
use Iterator;
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

    protected string $resourceKey = 'countries';

    protected string $pluralName = '';

    protected string $model = Timezone::class;

    protected string $insertionMode = self::INDIVIDUAL_INSERTION_MODE;

    public function __construct()
    {
        parent::__construct();
        $this->pluralName = EntitiesEnum::Timezones->value;
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

    /**
     * @param Timezone $instance
     */
    protected function whenRecordInserted(BaseModel $instance): void
    {
        foreach ($this->data as $rawContry) {
            /** @var array{
             *      id: int,
             *      timezones: array<array<string,string>>
             *  } $rawCountry
             * */
            foreach ($rawContry['timezones'] as $rawTimezone) {
                if ($rawTimezone['zoneName'] === $instance->zone_name) {
                    $instance->countries()->attach($rawContry['id']);

                    continue 2;
                }
            }
        }
    }
}
