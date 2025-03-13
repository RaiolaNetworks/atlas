<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCitiesTable extends Migration
{
    private string $tableName;

    public function __construct()
    {
        $this->tableName = config()->string('atlas.cities_tablename');
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! config()->boolean('atlas.entities.cities')) {
            return;
        }

        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('state_id')->constrained(config()->string('atlas.states_tablename'));
            $table->string('state_code', 5);
            $table->string('state_name');
            $table->foreignId('country_id')->constrained(config()->string('atlas.countries_tablename'));
            $table->string('country_code', 3);
            $table->string('country_name');
            $table->string('latitude');
            $table->string('longitude');
            $table->string('wiki_data_id')->nullable();

            $table->index(['country_id', 'state_id']);
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
