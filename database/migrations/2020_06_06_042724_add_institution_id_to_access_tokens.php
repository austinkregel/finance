<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInstitutionIdToAccessTokens extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('access_tokens', function (Blueprint $table): void {
            $table->string('institution_id')->index()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('access_tokens', function (Blueprint $table): void {
            $table->dropColumn('institution_id');
        });
    }
}
