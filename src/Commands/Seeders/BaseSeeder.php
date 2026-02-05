<?php

declare(strict_types=1);

namespace Raiolanetworks\Atlas\Commands\Seeders;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Iterator;
use Raiolanetworks\Atlas\Helpers\ResourcesManager;
use Raiolanetworks\Atlas\Models\BaseModel;
use ReflectionException;
use ReflectionMethod;
use ReflectionNamedType;
use Symfony\Component\Console\Helper\ProgressBar;
use Throwable;

/**
 * @method void whenRecordInserted(BaseModel $instance) Require runtime check
 */
abstract class BaseSeeder extends Command
{
    protected const INDIVIDUAL_INSERTION_MODE = 'individual';

    private const BULK_INSERTION_MODE = 'bulk';

    /**
     * Maximum number of parts into which the entire data set is divided for incremental storage in the database.
     */
    protected const CHUNK_STEPS = 500;

    /**
     * Json resource key
     */
    protected string $resourceKey;

    /**
     * File data path
     */
    private string $dataPath;

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
     * @var class-string<BaseModel>
     */
    protected string $model;

    protected string $insertionMode = self::BULK_INSERTION_MODE;

    public function __construct()
    {
        parent::__construct();
        $this->dataPath = ResourcesManager::getResourcePath($this->resourceKey);
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
        $existsWhenRecordInsertedMethod = $this->existsWhenRecordInsertedMethod();
        $bar = $this->output->createProgressBar(count($this->data));
        $bar->start();

        try {
            DB::transaction(function () use ($bar, $existsWhenRecordInsertedMethod): void {
                Schema::disableForeignKeyConstraints();
                $this->model::truncate();
                Schema::enableForeignKeyConstraints();

                foreach (array_chunk($this->data, self::CHUNK_STEPS) as $chunk) {
                    $this->processChunk($chunk, $bar, $existsWhenRecordInsertedMethod);
                }
            });
        } catch (Throwable $th) {
            $this->newLine();
            $this->error('Something happened when trying to save the data...');
            $this->error('Error: ' . $th->getMessage());

            return false;
        }
        $bar->finish();
        $this->newLine();

        $this->info(Str::ucfirst($this->pluralName) . ' seeding in database correctly!');

        return true;
    }

    /**
     * @param array<mixed> $chunk
     */
    private function processChunk(array &$chunk, ProgressBar $progressBar, bool $existsWhenRecordInsertedMethod): void
    {
        switch ($this->insertionMode) {
            case self::BULK_INSERTION_MODE:
                $this->processChunkByBulkInsertion($chunk, $existsWhenRecordInsertedMethod, $progressBar);

                break;

            case self::INDIVIDUAL_INSERTION_MODE:
                $this->processChunkByIndividualInsertion($chunk, $existsWhenRecordInsertedMethod, $progressBar);

                break;
        }
    }

    /**
     * @param array<array<string,mixed>> $chunk
     */
    private function processChunkByBulkInsertion(array &$chunk, bool $existsWhenRecordInsertedMethod, ProgressBar $progressBar): void
    {
        $bulk = [];

        foreach ($chunk as $value) {
            foreach (static::generateElementsOfBulk($value) as $element) {
                $bulk[] = $element;
                $progressBar->advance();
            }
        }

        $this->model::query()->insert($bulk);

        if ($existsWhenRecordInsertedMethod) {
            $collect = collect($bulk)
                ->pluck((new $this->model)->getKeyName());

            if ($collect->count() === 0) {
                throw new Exception('The primary key is not defined in the inserted row. This is mandatory when you define whenRecordInserted method. Its mandatory define the primary key in the row.');
            }

            $collect->each(fn (string|int $id) => $this->whenRecordInserted($this->model::query()->findOrFail($id)));
        }
    }

    /**
     * @param array<array<string,mixed>> $chunk
     */
    private function processChunkByIndividualInsertion(array &$chunk, bool $existsWhenRecordInsertedMethod, ProgressBar $progressBar): void
    {
        foreach ($chunk as $value) {
            foreach (static::generateElementsOfBulk($value) as $element) {
                $instance = $this->model::query()->create($element);

                if ($existsWhenRecordInsertedMethod) {
                    $this->whenRecordInserted($instance);
                }

                $progressBar->advance();
            }
        }
    }

    private function existsWhenRecordInsertedMethod(): bool
    {
        if (! method_exists($this, 'whenRecordInserted')) {
            return false;
        }

        try {
            $reflection = ReflectionMethod::createFromMethodName(get_class($this) . '::whenRecordInserted');
            $parameters = $reflection->getParameters();

            if (count($parameters) === 0) {
                return false;
            }

            $firstParameter = $parameters[0];

            if (! $firstParameter->hasType()) {
                return false;
            }

            $paramType = $firstParameter->getType();

            if (! $paramType instanceof ReflectionNamedType) {
                return false;
            }

            return is_a($paramType->getName(), BaseModel::class, true);
        } catch (ReflectionException $e) {
            return false;
        }
    }

    /**
     * @param  array<string, mixed>           $jsonItem
     * @return Iterator<array<string, mixed>>
     */
    protected function generateElementsOfBulk(array $jsonItem): Iterator
    {
        yield $this->model::fromJsonToDBRecord($jsonItem);
    }
}
