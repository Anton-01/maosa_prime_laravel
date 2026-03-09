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
        Schema::table('user_price_lists', function (Blueprint $table) {
            $table->foreignId('user_branch_id')->nullable()->after('user_id')
                ->constrained('user_branches')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_price_lists', function (Blueprint $table) {
            $table->dropForeign(['user_branch_id']);
            $table->dropColumn('user_branch_id');
        });
    }
};
