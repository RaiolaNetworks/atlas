<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /** @var string $tableName */
        $tableName = config('atlas.countries_tablename');

        Schema::create($tableName, function (Blueprint $table) {
            $table->id();
            $table->string('iso2', 2);
            $table->string('name');
            $table->tinyInteger('status')->default(1);
            $table->string('phone_code', 5);
            $table->string('iso3', 3);
            $table->string('currency', 3)->nullable();
            $table->string('native')->nullable();
            $table->string('region')->nullable();
            $table->string('subregion')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
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
        $tableName = config('atlas.countries_tablename');

        Schema::dropIfExists($tableName);
    }
}
