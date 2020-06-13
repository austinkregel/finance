<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountKpisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('account_kpis', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date');
            $table->string('account_id')->index();
            $table->double('balance');
            $table->double('available');
            $table->integer('total_transactions_today')->default(0);
            $table->integer('total_subscriptions_today')->default(0);
            $table->integer('total_bills_today')->default(0);
            $table->integer('total_spends_today')->default(0);
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
        Schema::dropIfExists('account_kpis');
    }
}
