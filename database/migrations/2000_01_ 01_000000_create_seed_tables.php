<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateSeedTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        \Illuminate\Support\Facades\DB::getPdo()->exec(file_get_contents(__DIR__ . '/categories_schema_seed.sql'));
        if (empty($_ENV['TEST_COMMAND'])) {
            \Illuminate\Support\Facades\DB::getPdo()->exec(file_get_contents(__DIR__ . '/categories_seed.sql'));
        }
        \Illuminate\Support\Facades\DB::getPdo()->exec(file_get_contents(__DIR__ . '/institutions_schema_seed.sql'));

        if (empty($_ENV['TEST_COMMAND'])) {
            $contents = file_get_contents(__DIR__ . '/institutions_seed.sql');
            $contents = \Illuminate\Support\Collection::make(explode("\n", $contents));
            $contents->chunk(1000)->each(function (Illuminate\Support\Collection $chunks): void {
                \Illuminate\Support\Facades\DB::getPdo()->exec($chunks->join("\n"));
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
}
