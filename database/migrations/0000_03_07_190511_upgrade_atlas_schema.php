<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->renamePivotColumn();
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
     * Rename time_zone_name → timezone_name in the country-timezone pivot table.
     *
     * Only applies to databases created before the column was renamed in the
     * original migration file (commit 0258d50).
     */
    private function renamePivotColumn(): void
    {
        $pivotTable = config()->string('atlas.country_timezone_pivot_tablename');

        if (! Schema::hasTable($pivotTable) || ! Schema::hasColumn($pivotTable, 'time_zone_name')) {
            return;
        }

        $timezonesTable = config()->string('atlas.timezones_tablename');

        Schema::table($pivotTable, function (Blueprint $table): void {
            $table->dropForeign(['time_zone_name']);
            $table->renameColumn('time_zone_name', 'timezone_name');
        });

        Schema::table($pivotTable, function (Blueprint $table) use ($timezonesTable): void {
            $table->foreign('timezone_name')->references('zone_name')->on($timezonesTable);
        });
    }

    /**
     * Make region_id nullable so that nullOnDelete() can work correctly.
     *
     * Only applies to databases where region_id was created as NOT NULL.
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
     * Add indexes that were added to CREATE TABLE migrations after initial release.
     *
     * Only applies to databases created before the indexes were added (commit 7716529).
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
    }
};
