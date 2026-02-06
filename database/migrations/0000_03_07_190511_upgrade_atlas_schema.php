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
        $this->fixCurrencyCodeForeignKey();
        $this->fixRegionIdNullability();
        $this->addMissingIndexes();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not reversible: column renames, nullability changes, foreign-key
        // fixes, and index additions are intentionally kept. The original
        // CREATE TABLE migrations handle full table drops.
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
     * Fix currency_code FK: make column nullable and re-create FK with nullOnDelete.
     */
    private function fixCurrencyCodeForeignKey(): void
    {
        $countriesTable   = config()->string('atlas.countries_tablename');
        $currenciesTable  = config()->string('atlas.currencies_tablename');

        if (! Schema::hasTable($countriesTable) || ! Schema::hasColumn($countriesTable, 'currency_code') || ! Schema::hasTable($currenciesTable)) {
            return;
        }

        Schema::table($countriesTable, function (Blueprint $table) use ($countriesTable): void {
            $fks = collect(Schema::getForeignKeys($countriesTable));

            if ($fks->contains(fn (array $fk): bool => in_array('currency_code', $fk['columns']))) {
                $table->dropForeign(['currency_code']);
            }
        });

        Schema::table($countriesTable, function (Blueprint $table) use ($currenciesTable): void {
            $table->string('currency_code', 3)->nullable()->change();
            $table->foreign('currency_code')->references('code')->on($currenciesTable)->nullOnDelete();
        });
    }

    /**
     * Make region_id nullable and re-create FK with nullOnDelete.
     */
    private function fixRegionIdNullability(): void
    {
        $countriesTable = config()->string('atlas.countries_tablename');
        $regionsTable   = config()->string('atlas.regions_tablename');

        if (! Schema::hasTable($countriesTable) || ! Schema::hasColumn($countriesTable, 'region_id') || ! Schema::hasTable($regionsTable)) {
            return;
        }

        Schema::table($countriesTable, function (Blueprint $table) use ($countriesTable): void {
            $fks = collect(Schema::getForeignKeys($countriesTable));

            if ($fks->contains(fn (array $fk): bool => in_array('region_id', $fk['columns']))) {
                $table->dropForeign(['region_id']);
            }
        });

        Schema::table($countriesTable, function (Blueprint $table) use ($regionsTable): void {
            $table->unsignedBigInteger('region_id')->nullable()->change();
            $table->foreign('region_id')->references('id')->on($regionsTable)->nullOnDelete();
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

            if (Schema::hasColumn($countriesTable, 'region_id')
                && ! Schema::hasIndex($countriesTable, "{$countriesTable}_region_id_index")
                && ! Schema::hasIndex($countriesTable, "{$countriesTable}_region_id_foreign")) {
                Schema::table($countriesTable, function (Blueprint $table): void {
                    $table->index('region_id');
                });
            }

            if (Schema::hasColumn($countriesTable, 'subregion_id')
                && ! Schema::hasIndex($countriesTable, "{$countriesTable}_subregion_id_index")
                && ! Schema::hasIndex($countriesTable, "{$countriesTable}_subregion_id_foreign")) {
                Schema::table($countriesTable, function (Blueprint $table): void {
                    $table->index('subregion_id');
                });
            }

            if (Schema::hasColumn($countriesTable, 'currency_code')
                && ! Schema::hasIndex($countriesTable, "{$countriesTable}_currency_code_index")
                && ! Schema::hasIndex($countriesTable, "{$countriesTable}_currency_code_foreign")) {
                Schema::table($countriesTable, function (Blueprint $table): void {
                    $table->index('currency_code');
                });
            }
        }

        $statesTable = config()->string('atlas.states_tablename');

        if (Schema::hasTable($statesTable) && ! Schema::hasIndex($statesTable, "{$statesTable}_state_code_index")) {
            Schema::table($statesTable, function (Blueprint $table): void {
                $table->index('state_code');
            });
        }

        if (Schema::hasTable($statesTable)
            && Schema::hasColumn($statesTable, 'country_id')
            && ! Schema::hasIndex($statesTable, "{$statesTable}_country_id_index")
            && ! Schema::hasIndex($statesTable, "{$statesTable}_country_id_foreign")) {
            Schema::table($statesTable, function (Blueprint $table): void {
                $table->index('country_id');
            });
        }

        $subregionsTable = config()->string('atlas.subregions_tablename');

        if (Schema::hasTable($subregionsTable)
            && Schema::hasColumn($subregionsTable, 'region_id')
            && ! Schema::hasIndex($subregionsTable, "{$subregionsTable}_region_id_index")
            && ! Schema::hasIndex($subregionsTable, "{$subregionsTable}_region_id_foreign")) {
            Schema::table($subregionsTable, function (Blueprint $table): void {
                $table->index('region_id');
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

        DB::transaction(function () use ($pivotTable, $duplicates): void {
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
        });
    }
};
