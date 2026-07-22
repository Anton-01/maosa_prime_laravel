<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Blog posts (renamed "Seminarios" in the UI) no longer use a category, so
 * drop the blog_category_id foreign key and column. Guarded by hasColumn.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('blogs', 'blog_category_id')) {
            Schema::table('blogs', function (Blueprint $table) {
                $table->dropForeign(['blog_category_id']);
                $table->dropColumn('blog_category_id');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('blogs', 'blog_category_id')) {
            Schema::table('blogs', function (Blueprint $table) {
                $table->foreignId('blog_category_id')->nullable()->constrained('blog_categories')->nullOnDelete();
            });
        }
    }
};
