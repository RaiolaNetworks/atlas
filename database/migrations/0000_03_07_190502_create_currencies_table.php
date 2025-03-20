<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCurrenciesTable extends Migration
{
    private string $tableName;

    public function __construct()
    {
        $this->tableName = config()->string('atlas.currencies_tablename');
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! config()->boolean('atlas.entities.currencies')) {
            return;
        }

        Schema::create($this->tableName, function (Blueprint $table) {
            $table->string('code', 3)->primary();
            $table->string('name');
            $table->string('symbol');
            $table->string('symbol_native');
            $table->string('thousands_separator', 1)->default(',');
            $table->tinyInteger('decimal_digits', false, true)->default('2');
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
