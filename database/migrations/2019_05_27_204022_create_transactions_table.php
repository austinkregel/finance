<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->string('name')->nullable()->index();
            $table->double('amount', 13, 2)->nullable();
            $table->string('account_id')->nullable()->index();
            $table->date('date')->nullable();
            $table->boolean('pending')->default(false);
            $table->integer('category_id')->unsigned()->nullable();
            $table->string('transaction_id')->nullable()->index();
            $table->string('transaction_type')->nullable();
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
        Schema::dropIfExists('transactions');
    }
}
