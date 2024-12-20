<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimezonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (config('atlas.modules.timezones') === false) {
            return;
        }

        /** @var string $tableName */
        $tableName = config('atlas.timezones_tablename');

        Schema::create($tableName, function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id');
            $table->string('name');
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
        $tableName = config('atlas.timezones_tablename');

        Schema::dropIfExists($tableName);
    }
}
