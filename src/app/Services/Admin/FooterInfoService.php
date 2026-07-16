<?php

namespace App\Services\Admin;

use App\Models\FooterInfo;

class FooterInfoService
{
    /**
     * The singleton footer info shaped for the frontend form.
     *
     * @return array<string, string|null>
     */
    public function getAsFormValues(): array
    {
        $footerInfo = FooterInfo::first();

        return [
            'short_description' => $footerInfo?->short_description,
            'address' => $footerInfo?->address,
            'email' => $footerInfo?->email,
            'phone' => $footerInfo?->phone,
            'copyright' => $footerInfo?->copyright,
        ];
    }

    /**
     * @param array<string, mixed> $data Validated payload.
     */
    public function update(array $data): FooterInfo
    {
        return FooterInfo::updateOrCreate(
            ['id' => 1],
            [
                'short_description' => $data['short_description'],
                'address' => $data['address'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'copyright' => $data['copyright'],
            ],
        );
    }
}
