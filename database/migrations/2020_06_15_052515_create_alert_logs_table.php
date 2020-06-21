<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlertLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alert_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('triggered_by_transaction_id');
            $table->unsignedInteger('triggered_by_tag_id')->nullable();
            $table->unsignedInteger('alert_id');
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
        Schema::dropIfExists('alert_logs');
    }
}
