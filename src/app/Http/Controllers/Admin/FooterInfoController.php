<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FooterInfoUpdateRequest;
use App\Services\Admin\FooterInfoService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class FooterInfoController extends Controller
{
    public function __construct(private readonly FooterInfoService $footerInfoService)
    {
        $this->middleware(['permission:access management footer']);
    }

    public function index(): Response
    {
        return Inertia::render('Admin/FooterInfo/Index', [
            'footerInfo' => $this->footerInfoService->getAsFormValues(),
            'urls' => [
                'update' => route('admin.footer-info.update'),
            ],
        ]);
    }

    public function update(FooterInfoUpdateRequest $request): RedirectResponse
    {
        $this->footerInfoService->update($request->validated());

        return back()->with('success', '¡Información del footer actualizada correctamente!');
    }
}
