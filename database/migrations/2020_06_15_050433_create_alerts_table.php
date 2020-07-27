<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlertsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('title', 180)->nullable();
            $table->string('body', 260)->nullable();
            $table->json('payload')->nullable();
            $table->json('channels');
            $table->addColumn('smallInteger', 'is_templated')->storedAs(
                '(INSTR(`body`, \'{{\') or INSTR(`body`, \'}}\') or INSTR(`title`, \'{{\') or INSTR(`title`, \'}}\'))'
            );
            $table->string('webhook_url', 2048)->nullable();
            // For slack channels/discord channels.
            $table->string('messaging_service_channel')->nullable();
            // These are the events this alert is related to. This gives users more control over their alerts.
            $table->json('events')->nullable();
            $table->boolean('must_all_conditions_pass')->default(true);
            // The owner
            $table->unsignedInteger('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('alerts');
    }
}
