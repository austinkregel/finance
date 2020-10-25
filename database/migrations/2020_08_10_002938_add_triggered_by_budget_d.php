<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTriggeredByBudgetD extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('alert_logs', function (Blueprint $table): void {
            $table->unsignedInteger('triggered_by_transaction_id')->nullable()->change();
            $table->unsignedInteger('triggered_by_budget_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('alert_logs', function (Blueprint $table): void {
            $table->unsignedInteger('triggered_by_transaction_id')->change();
            $table->dropColumn('triggered_by_budget_id');
        });
    }
}
