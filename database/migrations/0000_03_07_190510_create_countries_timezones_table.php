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
        $this->tableName = config()->string('atlas.country_timezone_pivot_tablename');
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! config()->boolean('atlas.entities.timezones') || ! config()->boolean('atlas.entities.countries')) {
            return;
        }

        Schema::create($this->tableName, function (Blueprint $table) {
            $table->foreignId('country_id')->constrained(config()->string('atlas.countries_tablename'));

            $table->string('timezone_name');
            $table->foreign('timezone_name')->references('zone_name')->on(config()->string('atlas.timezones_tablename'));
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
