<?php

declare(strict_types=1);

namespace Raiolanetworks\Atlas\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use pcrov\JsonReader\JsonReader;
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

    public function handle(): int
    {
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

        return true;
    }

    /**
     * Collects the data from the json file and creates the records in the corresponding table
     */
    protected function seed(): bool
    {
        $this->model::truncate();

        try {
            $reader = new JsonReader;
            $reader->open($this->dataPath);

            $reader->read(); // Begin array
            $reader->read(); // First element, or end of array

            $bulk = [];

            while ($reader->type() === JsonReader::OBJECT) {
                /** @var array<string, mixed>|null $data */
                $data = $reader->value();

                if ($data === null) {
                    throw new Exception('Any element has failed when read json file: ' . $this->dataPath);
                }

                $bulk[] = $this->parseItem($data);

                if (count($bulk) >= self::CHUNK_STEPS) {
                    $this->saveBulkAndReset($bulk);
                }

                $reader->next();
            }

            $this->saveBulkAndReset($bulk);

            $reader->close();
        } catch (Throwable $th) {
            $this->error('Something happened when trying to save the data...');
            $this->error($th->getMessage());

            return false;
        }

        $this->newLine();

        $this->info(Str::ucfirst($this->pluralName) . ' seeding in database correctly!');

        return true;
    }

    /**
     * @param array<array<string, mixed>> $bulk
     */
    private function saveBulkAndReset(array &$bulk): void
    {
        $this->model::query()->insert($bulk);
        $bulk = [];
    }

    /**
     * Prepares the data with the actual values before storing in the database
     *
     * @param  array<string,mixed> $rawItem
     * @return array<string,mixed>
     */
    abstract protected function parseItem(array $rawItem): array;
}
