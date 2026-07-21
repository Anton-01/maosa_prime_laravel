<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The amenity icon (FontAwesome class) is no longer used anywhere in the
 * admin or the public site, so drop the column. Guarded by hasColumn.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('amenities', 'icon')) {
            Schema::table('amenities', function (Blueprint $table) {
                $table->dropColumn('icon');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('amenities', 'icon')) {
            Schema::table('amenities', function (Blueprint $table) {
                $table->string('icon')->nullable();
            });
        }
    }
};
