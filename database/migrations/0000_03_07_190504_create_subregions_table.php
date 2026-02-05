<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $tableName;

    public function __construct()
    {
        $this->tableName = config()->string('atlas.subregions_tablename');
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! config()->boolean('atlas.entities.subregions')) {
            return;
        }

        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->string('name');

            if (config()->boolean('atlas.entities.regions')) {
                $table->foreignId('region_id')->constrained(config()->string('atlas.regions_tablename'));
            }

            $table->json('translations');
            $table->string('wiki_data_id', 16);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
