<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountKpisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('account_kpis', function (Blueprint $table): void {
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
    public function down(): void
    {
        Schema::dropIfExists('account_kpis');
    }
}
