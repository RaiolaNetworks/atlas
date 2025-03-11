<?php

declare(strict_types=1);

namespace Raiolanetworks\Atlas\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Throwable;

abstract class BaseSeeder extends Command
{
    /**
     * Maximum number of parts into which the entire data set is divided for incremental storage in the database.
     */
    protected const CHUNK_STEPS = 500;

    /**
     * File data path
     */
    protected string $dataPath;

    protected string $overridedResourcesDataPath;

    /**
     * Data of the data file
     *
     * @var array<mixed>
     */
    protected array $data = [];

    /**
     * Name of the entity in plural
     */
    protected string $pluralName = '';

    /**
     * Model name
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected string $model;

    public function __construct()
    {
        parent::__construct();

        $resourcePath = resource_path($this->overridedResourcesDataPath);

        if (file_exists($resourcePath)) {
            $this->dataPath = $resourcePath;
        }
    }

    public function handle(): int
    {
        ini_set('memory_limit', '-1');

        if (! $this->checkDataFile()) {
            return self::FAILURE;
        }

        if (! class_exists($this->model)) {
            $this->error("Class model ({$this->model}) not found");

            return self::FAILURE;
        }

        if (! $this->seed()) {
            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    /**
     * Check if data file exist
     */
    protected function checkDataFile(): bool
    {
        if (! file_exists($this->dataPath)) {
            $this->error('The file for seeding the ' . Str::lower($this->pluralName) . ' was not found...');

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
     * Collects the data from the json file and creates the records in the corresponding table
     */
    protected function seed(): bool
    {
        $this->model::truncate();
        $bar = $this->output->createProgressBar(count($this->data));
        $bar->start();

        try {
            foreach (array_chunk($this->data, self::CHUNK_STEPS) as $chunk) {
                $bulk = [];

                foreach ($chunk as $value) {
                    /** @var array<string,mixed> $value */
                    $this->parseItem($value, $bulk);
                }

                $this->model::query()->insert($bulk);

                $bar->advance(self::CHUNK_STEPS);
            }
        } catch (Throwable $th) {
            $this->error('Something happened when trying to save the data...');

            return false;
        }
        $bar->finish();
        $this->newLine();

        $this->info(Str::ucfirst($this->pluralName) . ' seeding in database correctly!');

        return true;
    }

    /**
     * Prepares the data with the actual values before storing in the database
     *
     * @param array<string,mixed> $rawItem
     * @param array<string,mixed> &$bulk
     */
    abstract protected function parseItem(array $rawItem, array &$bulk): void;
}
