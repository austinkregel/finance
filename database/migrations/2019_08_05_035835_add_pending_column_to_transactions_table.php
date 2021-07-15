<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPendingColumnToTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table): void {
            $table->boolean('is_subscription')->default(null)->nullable()->after('name')->index();
            $table->boolean('is_possible_subscription')->default(null)->nullable()->after('name')->index();
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
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table): void {
            $table->float('amount')->change();
            $table->dropColumn(['is_subscription', 'is_possible_subscription']);
            $table->dropIndex([
                'name',
                'date',
            ]);
        });
    }
}
