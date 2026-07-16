<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hero;
use App\Services\Admin\HeroService;
use App\Services\Admin\OurFeatureService;
use App\Services\Admin\SectionTitleService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Unified "Secciones" screen: private/public banners, features and
 * section titles live together in one tabbed Inertia page.
 */
class SectionController extends Controller
{
    public function __construct(
        private readonly HeroService $heroService,
        private readonly OurFeatureService $ourFeatureService,
        private readonly SectionTitleService $sectionTitleService,
    ) {
        $this->middleware(['permission:access management sections content']);
    }

    public function index(Request $request): Response
    {
        return Inertia::render('Admin/Sections/Index', [
            'initialTab' => $request->query('tab', 'private-banner'),
            'privateHero' => $this->heroPayload($this->heroService->findActiveByType(HeroService::TYPE_PRIVATE)),
            'publicHero' => $this->heroPayload($this->heroService->findActiveByType(HeroService::TYPE_PUBLIC)),
            'features' => $this->ourFeatureService->list(),
            'sectionTitles' => $this->sectionTitleService->getAsFormValues(),
            'urls' => [
                'privateHeroUpdate' => route('admin.hero.update'),
                'publicHeroUpdate' => route('admin.hero.public.update'),
                'featuresBase' => route('admin.our-features.store'),
                'sectionTitlesUpdate' => route('admin.section-title.update'),
            ],
        ]);
    }

    /**
     * @return array<string, string|null>|null
     */
    private function heroPayload(?Hero $hero): ?array
    {
        if (! $hero) {
            return null;
        }

        return [
            'title' => $hero->title,
            'subTitle' => $hero->sub_title,
            'background' => $hero->background,
            'backgroundUrl' => $hero->background ? asset($hero->background) : null,
        ];
    }
}
