<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds admin_level and parent_id columns to the states table for
     * hierarchical administrative divisions. Every operation is guarded
     * for idempotency so this migration is safe on both fresh installs
     * and upgrades.
     */
    public function up(): void
    {
        if (! config()->boolean('atlas.entities.states')) {
            return;
        }

        $statesTable = config()->string('atlas.states_tablename');

        if (! Schema::hasTable($statesTable)) {
            return;
        }

        if (! Schema::hasColumn($statesTable, 'admin_level')) {
            Schema::table($statesTable, function (Blueprint $table): void {
                $table->unsignedTinyInteger('admin_level')->default(1)->after('type');
            });
        }

        if (! Schema::hasColumn($statesTable, 'parent_id')) {
            Schema::table($statesTable, function (Blueprint $table) use ($statesTable): void {
                $table->unsignedBigInteger('parent_id')->nullable()->after('admin_level');

                $table->foreign('parent_id')
                    ->references('id')
                    ->on($statesTable)
                    ->nullOnDelete();
            });
        }

        $this->addMissingIndexes($statesTable);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not reversible: column additions and indexes are intentionally kept.
        // The original CREATE TABLE migration handles full table drops.
    }

    /**
     * Add indexes for the new columns if they don't already exist.
     */
    private function addMissingIndexes(string $statesTable): void
    {
        if (! Schema::hasIndex($statesTable, "{$statesTable}_admin_level_index")) {
            Schema::table($statesTable, function (Blueprint $table): void {
                $table->index('admin_level');
            });
        }

        if (! Schema::hasIndex($statesTable, "{$statesTable}_parent_id_index")
            && ! Schema::hasIndex($statesTable, "{$statesTable}_parent_id_foreign")) {
            Schema::table($statesTable, function (Blueprint $table): void {
                $table->index('parent_id');
            });
        }

        if (! Schema::hasIndex($statesTable, "{$statesTable}_country_id_admin_level_index")) {
            Schema::table($statesTable, function (Blueprint $table): void {
                $table->index(['country_id', 'admin_level']);
            });
        }
    }
};
