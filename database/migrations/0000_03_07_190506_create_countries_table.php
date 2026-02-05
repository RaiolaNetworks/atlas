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
            $table->string('name', 80);
            $table->string('iso2', 2);
            $table->string('iso3', 3);
            $table->string('numeric_code', 3);
            $table->string('phonecode', 5);
            $table->string('capital', 80)->nullable();

            if (config()->boolean('atlas.entities.currencies')) {
                $table->string('currency_code', 3);
                $table->foreign('currency_code')->references('code')->on(config()->string('atlas.currencies_tablename'))->cascadeOnDelete();
            }

            $table->string('tld', 8);
            $table->string('native', 80)->nullable();
            $table->string('region', 80);

            if (config()->boolean('atlas.entities.regions')) {
                $table->foreignId('region_id')->constrained(config()->string('atlas.regions_tablename'))->nullOnDelete();
            }
            $table->string('subregion', 80)->nullable();

            if (config()->boolean('atlas.entities.subregions')) {
                $table->foreignId('subregion_id')->nullable()->constrained(config()->string('atlas.subregions_tablename'))->nullOnDelete();
            }
            $table->string('nationality', 80);
            $table->json('translations');
            $table->string('latitude', 15);
            $table->string('longitude', 15);
            $table->string('emoji', 40);
            $table->string('emojiU', 40);

            $table->index('iso2');
            $table->index('iso3');
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
