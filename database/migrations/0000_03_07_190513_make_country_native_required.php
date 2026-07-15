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
     * Enforces a non-null `native` on the countries table. Databases that
     * were migrated before this constraint existed may hold null natives,
     * so those rows are first backfilled from `name` (mirroring the seeder's
     * `native ?? name` fallback) to guarantee the NOT NULL change succeeds.
     * Every step is guarded so the migration is safe on both fresh installs
     * and upgrades.
     */
    public function up(): void
    {
        if (! config()->boolean('atlas.entities.countries')) {
            return;
        }

        $countriesTable = config()->string('atlas.countries_tablename');

        if (! Schema::hasTable($countriesTable) || ! Schema::hasColumn($countriesTable, 'native')) {
            return;
        }

        // Backfill existing NULLs with the country name so the NOT NULL
        // change cannot fail on already-seeded production databases.
        // `name` is a non-reserved identifier across MySQL/PostgreSQL/SQLite.
        DB::table($countriesTable)
            ->whereNull('native')
            ->update(['native' => DB::raw('name')]);

        Schema::table($countriesTable, function (Blueprint $table): void {
            $table->string('native', 80)->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * Restores the nullable definition; existing data is left untouched.
     */
    public function down(): void
    {
        if (! config()->boolean('atlas.entities.countries')) {
            return;
        }

        $countriesTable = config()->string('atlas.countries_tablename');

        if (! Schema::hasTable($countriesTable) || ! Schema::hasColumn($countriesTable, 'native')) {
            return;
        }

        Schema::table($countriesTable, function (Blueprint $table): void {
            $table->string('native', 80)->nullable()->change();
        });
    }
};
