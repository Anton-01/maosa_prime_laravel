<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HeroUpdateRequest;
use App\Models\Hero;
use App\Traits\FileUploadTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HeroController extends Controller
{
    use FileUploadTrait;

    function __construct()
    {
        $this->middleware(['permission:section index'])->only(['index']);
        $this->middleware(['permission:section update'])->only(['update']);
    }

    function index() : View {
        $hero = Hero::where(['type' => 'private', 'active' => 1])->first();
        return view('admin.hero.index', compact('hero'));
    }

    function indexPublic() : View {
        $hero = Hero::where(['type' => 'public', 'active' => 1])->first();
        return view('admin.hero.public', compact('hero'));
    }

    function update(HeroUpdateRequest $request) : RedirectResponse {

        $imagePath = $this->uploadImage($request, 'background', $request->old_background);

        $hero = Hero::where(['type' => 'private', 'active' => 1])->first();

        $hero->update([
            'background' => !empty($imagePath) ? $imagePath : $request->old_background,
            'title' => $request->title,
            'sub_title' => $request->sub_title
        ]);

        return back()->with('statusHero', true);
    }

    function updatePublic(HeroUpdateRequest $request) : RedirectResponse {

        $imagePath = $this->uploadImage($request, 'background', $request->old_background);

        $hero = Hero::where(['type' => 'public', 'active' => 1])->first();

        $hero->update([
            'background' => !empty($imagePath) ? $imagePath : $request->old_background,
            'title' => $request->title,
            'sub_title' => $request->sub_title
        ]);

        return back()->with('statusHeroPublic', true);
    }
}
