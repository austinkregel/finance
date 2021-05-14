<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTagTables extends Migration
{
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table): void {
            $table->increments('id');
            $table->json('name');
            $table->json('slug');
            $table->string('type')->nullable();
            $table->boolean('must_all_conditions_pass')->default(true);
            $table->integer('order_column')->nullable();
            $table->bigInteger('user_id')->unsigned();
            $table->timestamps();
        });

        Schema::create('taggables', function (Blueprint $table): void {
            $table->integer('tag_id')->unsigned();
            $table->morphs('taggable');

            $table->unique(['tag_id', 'taggable_id', 'taggable_type']);

            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::drop('taggables');
        Schema::drop('tags');
    }
}
