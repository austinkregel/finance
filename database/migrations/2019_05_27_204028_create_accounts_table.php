<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('account_id')->index();
            $table->integer('access_token_id')->unsigned();
            $table->string('mask')->nullable();
            $table->string('name')->nullable()->index();
            $table->string('official_name')->nullable();
            $table->double('balance')->default(0.0);
            $table->double('available')->default(0.0);
            $table->string('subtype')->nullable();
            $table->string('type');
            $table->timestamps();
        });

        Schema::create('account_users', function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
            $table->integer('account_id')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounts');
        Schema::dropIfExists('account_users');
    }
}
