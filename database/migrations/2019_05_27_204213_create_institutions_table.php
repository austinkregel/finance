<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInstitutionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('institutions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('institution_id')->index();
            $table->string('logo_url', 2048)->nullable();
            $table->string('products', 2048)->nullable();
            $table->timestamps();

            $table->unique(['name', 'institution_id']);
        });

        Schema::create('institution_accounts', function (Blueprint $table) {
            $table->integer('institution_id')->unsigned()->index();
            $table->integer('account_id')->unsigned()->index();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('institutions');
        Schema::dropIfExists('institution_accounts');
    }
}
