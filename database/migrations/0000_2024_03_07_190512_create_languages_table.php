<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (config('atlas.modules.languages') === false) {
            return;
        }

        /** @var string $tableName */
        $tableName = config('atlas.languages_tablename');

        Schema::create($tableName, function (Blueprint $table) {
            $table->id();
            $table->char('code', 2);
            $table->string('name');
            $table->string('name_native');
            $table->char('dir', 3);
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
        $tableName = config('atlas.languages_tablename');

        Schema::dropIfExists($tableName);
    }
}
