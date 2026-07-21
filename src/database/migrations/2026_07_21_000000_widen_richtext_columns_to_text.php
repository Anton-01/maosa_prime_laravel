<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The admin now edits these fields with a rich-text (WYSIWYG) editor, so they
 * store HTML markup that easily exceeds the original VARCHAR(255) length.
 * Widen them to TEXT. Idempotent and guarded by hasColumn.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('heroes', 'sub_title')) {
            Schema::table('heroes', function (Blueprint $table) {
                $table->text('sub_title')->change();
            });
        }

        if (Schema::hasColumn('our_features', 'short_description')) {
            Schema::table('our_features', function (Blueprint $table) {
                $table->text('short_description')->change();
            });
        }

        if (Schema::hasColumn('footer_infos', 'short_description')) {
            Schema::table('footer_infos', function (Blueprint $table) {
                $table->text('short_description')->change();
            });
        }

        $sectionSubtitles = [
            'our_feature_sub_title',
            'our_categories_sub_title',
            'our_location_sub_title',
            'our_featured_listing_sub_title',
            'our_our_pricing_sub_title',
            'our_testimonial_sub_title',
            'our_blog_sub_title',
        ];

        foreach ($sectionSubtitles as $column) {
            if (Schema::hasColumn('section_titles', $column)) {
                Schema::table('section_titles', function (Blueprint $table) use ($column) {
                    $table->text($column)->nullable()->change();
                });
            }
        }
    }

    public function down(): void
    {
        // Intentionally not reverting to VARCHAR(255): narrowing could truncate
        // existing HTML content and lose data.
    }
};
