<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConditionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('conditions', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->string('parameter')->nullable();
            $table->string('comparator')->nullable();
            $table->string('value')->nullable();

            $table->morphs('conditionable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('conditions');
    }
}
