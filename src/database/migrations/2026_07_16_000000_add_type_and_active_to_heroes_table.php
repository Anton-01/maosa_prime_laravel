<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Regularization: the "type" and "active" columns were added directly in
 * the database (the application filters heroes by them) but no migration
 * ever declared them. Guarded so environments that already have the
 * columns are unaffected.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('heroes', function (Blueprint $table) {
            if (! Schema::hasColumn('heroes', 'type')) {
                $table->string('type')->default('private');
            }

            if (! Schema::hasColumn('heroes', 'active')) {
                $table->boolean('active')->default(true);
            }
        });
    }

    public function down(): void
    {
        Schema::table('heroes', function (Blueprint $table) {
            if (Schema::hasColumn('heroes', 'type')) {
                $table->dropColumn('type');
            }

            if (Schema::hasColumn('heroes', 'active')) {
                $table->dropColumn('active');
            }
        });
    }
};
