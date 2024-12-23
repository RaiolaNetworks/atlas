<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCurrenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (config('atlas.modules.currencies') === false) {
            return;
        }

        /** @var string $tableName */
        $tableName = config('atlas.currencies_tablename');

        Schema::create($tableName, function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->index()->nullable();
            $table->string('name');
            $table->string('code');
            $table->string('symbol');
            $table->string('symbol_native');
            $table->string('thousands_separator', 1)->default(',');
            $table->tinyInteger('decimal_digits', false, true)->default('2');
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
        $tableName = config('atlas.currencies_tablename');

        Schema::dropIfExists($tableName);
    }
}
