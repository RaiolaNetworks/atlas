<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Applies all v2 schema changes. Every operation is guarded for
     * idempotency so this migration is safe on both fresh installs
     * (where the tables were just created by the original migrations)
     * and upgrades from v1.x.
     */
    public function up(): void
    {
        $this->renameCountryStringColumns();
        $this->fixRegionIdNullability();
        $this->addMissingIndexes();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not reversible: the original CREATE TABLE migrations handle table drops.
    }

    /**
     * Rename region → region_name and subregion → subregion_name on the countries table.
     */
    private function renameCountryStringColumns(): void
    {
        $countriesTable = config()->string('atlas.countries_tablename');

        if (! Schema::hasTable($countriesTable)) {
            return;
        }

        if (Schema::hasColumn($countriesTable, 'region')) {
            Schema::table($countriesTable, function (Blueprint $table): void {
                $table->renameColumn('region', 'region_name');
            });
        }

        if (Schema::hasColumn($countriesTable, 'subregion')) {
            Schema::table($countriesTable, function (Blueprint $table): void {
                $table->renameColumn('subregion', 'subregion_name');
            });
        }
    }

    /**
     * Make region_id nullable so that nullOnDelete() can work correctly.
     */
    private function fixRegionIdNullability(): void
    {
        $countriesTable = config()->string('atlas.countries_tablename');

        if (! Schema::hasTable($countriesTable) || ! Schema::hasColumn($countriesTable, 'region_id')) {
            return;
        }

        Schema::table($countriesTable, function (Blueprint $table): void {
            $table->unsignedBigInteger('region_id')->nullable()->change();
        });
    }

    /**
     * Add indexes introduced in v2.
     */
    private function addMissingIndexes(): void
    {
        $countriesTable = config()->string('atlas.countries_tablename');

        if (Schema::hasTable($countriesTable)) {
            if (! Schema::hasIndex($countriesTable, "{$countriesTable}_iso2_index")) {
                Schema::table($countriesTable, function (Blueprint $table): void {
                    $table->index('iso2');
                });
            }

            if (! Schema::hasIndex($countriesTable, "{$countriesTable}_iso3_index")) {
                Schema::table($countriesTable, function (Blueprint $table): void {
                    $table->index('iso3');
                });
            }
        }

        $statesTable = config()->string('atlas.states_tablename');

        if (Schema::hasTable($statesTable) && ! Schema::hasIndex($statesTable, "{$statesTable}_state_code_index")) {
            Schema::table($statesTable, function (Blueprint $table): void {
                $table->index('state_code');
            });
        }

        $citiesTable = config()->string('atlas.cities_tablename');

        if (Schema::hasTable($citiesTable) && ! Schema::hasIndex($citiesTable, "{$citiesTable}_name_index")) {
            Schema::table($citiesTable, function (Blueprint $table): void {
                $table->index('name');
            });
        }

        $pivotTable = config()->string('atlas.country_timezone_pivot_tablename');

        if (Schema::hasTable($pivotTable) && ! Schema::hasIndex($pivotTable, "{$pivotTable}_country_id_timezone_name_unique")) {
            $this->deduplicatePivotRows($pivotTable);

            Schema::table($pivotTable, function (Blueprint $table): void {
                $table->unique(['country_id', 'timezone_name']);
            });
        }
    }

    /**
     * Remove duplicate rows from the pivot table so the unique index can be added safely.
     */
    private function deduplicatePivotRows(string $pivotTable): void
    {
        $duplicates = DB::table($pivotTable)
            ->select('country_id', 'timezone_name')
            ->groupBy('country_id', 'timezone_name')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        if ($duplicates->isEmpty()) {
            return;
        }

        foreach ($duplicates as $row) {
            DB::table($pivotTable)
                ->where('country_id', $row->country_id)
                ->where('timezone_name', $row->timezone_name)
                ->delete();

            DB::table($pivotTable)->insert([
                'country_id'    => $row->country_id,
                'timezone_name' => $row->timezone_name,
            ]);
        }
    }
};
