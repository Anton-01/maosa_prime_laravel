<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetLocale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    /**
     * Guarda el idioma elegido y regresa a la pantalla anterior. El selector
     * envía un POST clásico (recarga completa) para que Ant Design, dayjs y
     * las traducciones se reinicien juntos en el nuevo idioma.
     */
    public function update(Request $request, string $locale): RedirectResponse
    {
        abort_unless(in_array($locale, SetLocale::SUPPORTED, true), 404);

        $request->session()->put(SetLocale::SESSION_KEY, $locale);

        return back();
    }
}
