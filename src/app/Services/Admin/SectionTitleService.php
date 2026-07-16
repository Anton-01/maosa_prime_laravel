<?php

namespace App\Services\Admin;

use App\Models\SectionTitle;

class SectionTitleService
{
    /**
     * Column names editable from the admin panel. The pricing and
     * testimonial pairs exist in the table but are not managed here yet.
     *
     * @var array<int, string>
     */
    public const FIELDS = [
        'our_feature_title',
        'our_feature_sub_title',
        'our_categories_title',
        'our_categories_sub_title',
        'our_location_title',
        'our_location_sub_title',
        'our_featured_listing_title',
        'our_featured_listing_sub_title',
        'our_blog_title',
        'our_blog_sub_title',
    ];

    public function get(): ?SectionTitle
    {
        return SectionTitle::first();
    }

    /**
     * Shape the singleton row as a flat map for the frontend form.
     *
     * @return array<string, string|null>
     */
    public function getAsFormValues(): array
    {
        $titles = $this->get();

        return collect(self::FIELDS)
            ->mapWithKeys(fn (string $field) => [$field => $titles?->{$field}])
            ->all();
    }

    /**
     * Create-or-update the singleton section titles row.
     *
     * @param array<string, mixed> $data Validated payload.
     */
    public function update(array $data): SectionTitle
    {
        $titles = $this->get() ?? new SectionTitle();

        foreach (self::FIELDS as $field) {
            $titles->{$field} = $data[$field] ?? null;
        }

        $titles->save();

        return $titles;
    }
}
