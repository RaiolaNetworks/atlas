<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountriesTimezonesTable extends Migration
{
    private string $tableName;

    public function __construct()
    {
        $this->tableName = config()->string('atlas.countries_timezones_pivot_tablename');
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (
            ! config()->boolean('atlas.entities.timezones')
        ) {
            return;
        }

        Schema::create($this->tableName, function (Blueprint $table) {
            $table->foreignId('country_id')->constrained(config()->string('atlas.countries_tablename'));
            $table->foreignId('time_zone_name')->constrained(config()->string('atlas.timezones_tablename'), 'zone_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
}
