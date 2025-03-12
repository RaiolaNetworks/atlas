<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountriesTable extends Migration
{
    private string $tableName;

    public function __construct()
    {
        $this->tableName = config()->string('atlas.countries_tablename');
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->string('currency', 3)->nullable();
            $table->string('iso2', 2);
            $table->string('name');
            $table->tinyInteger('status')->default(1);
            $table->string('phone_code', 5);
            $table->string('iso3', 3);
            $table->string('native')->nullable();
            $table->string('region')->nullable();
            $table->string('subregion')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
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
