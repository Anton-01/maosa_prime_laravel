<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SectionTitleUpdateRequest;
use App\Services\Admin\SectionTitleService;
use Illuminate\Http\RedirectResponse;

class SectionTitleController extends Controller
{
    public function __construct(private readonly SectionTitleService $sectionTitleService)
    {
        $this->middleware(['permission:access management sections content']);
    }

    /**
     * Legacy URL kept for old bookmarks: section titles now live in the
     * unified sections screen.
     */
    public function index(): RedirectResponse
    {
        return to_route('admin.sections.index', ['tab' => 'titles']);
    }

    public function update(SectionTitleUpdateRequest $request): RedirectResponse
    {
        $this->sectionTitleService->update($request->validated());

        return back()->with('success', '¡Títulos actualizados correctamente!');
    }
}
