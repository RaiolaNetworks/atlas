<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (config('atlas.modules.states') === false || config('atlas.modules.cities') === false) {
            return;
        }

        /** @var string $tableName */
        $tableName = config('atlas.cities_tablename');

        Schema::create($tableName, function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->index();
            $table->foreignId('state_id')->index();
            $table->string('name');
            $table->string('country_code', 3);
            $table->string('state_code', 5)->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();

            $table->index(['country_id', 'state_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /** @var string $tableName */
        $tableName = config('atlas.cities_tablename');

        Schema::dropIfExists($tableName);
    }
}
