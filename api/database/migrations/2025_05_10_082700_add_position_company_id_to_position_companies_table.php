<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add position_companies_id to company_user table
        Schema::table('company_user', function (Blueprint $table) {
            $table->unsignedBigInteger('position_companies_id')->nullable();

            $table->foreign('position_companies_id')
                  ->references('id')
                  ->on('position_companies')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop position_companies_id from company_user table
        Schema::table('company_user', function (Blueprint $table) {
            $table->dropForeign(['position_companies_id']);
            $table->dropColumn('position_companies_id');
        });
    }
};
