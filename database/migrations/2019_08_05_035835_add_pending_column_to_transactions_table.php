<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPendingColumnToTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->boolean('is_subscription')->default(null)->nullable()->after('name')->index();
            $table->boolean('is_possible_subscription')->default(null)->nullable()->after('name')->index();
            $table->string('amount')->change();
            $table->index([
                'name',
                'date',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->float('amount')->change();
            $table->dropColumn(['is_subscription', 'is_possible_subscription']);
            $table->dropIndex([
                'name',
                'date'
            ]);
        });
    }
}
