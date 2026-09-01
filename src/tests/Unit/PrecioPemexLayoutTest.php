<?php

use App\Http\Requests\PrecioPemexLayoutRequest;
use App\Models\User;
use App\Services\PrecioPemexApiService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

/*
 * Reglas del submódulo "Precios PEMEX": fecha de vigencia limitada a ayer,
 * hoy y mañana, y estaciones sin repetidos y siempre dentro de las asignadas
 * al usuario. No tocan base de datos (las estaciones asignadas se sustituyen),
 * por eso viven en Unit y no arrastran RefreshDatabase.
 */
uses(TestCase::class);

/** Usuario con estaciones asignadas fijas, sin ir a la pivote. */
class UsuarioConEstaciones extends User
{
    /** @var array<int, int> */
    public static array $estaciones = [1649, 200, 7];

    public function estacionesAsignadasIds(): array
    {
        return static::$estaciones;
    }
}

function peticionDeLayout(string $query): PrecioPemexLayoutRequest
{
    $request = PrecioPemexLayoutRequest::create('/api/precio_pemex/layout/HTML?' . $query, 'GET');
    $request->setUserResolver(fn () => new UsuarioConEstaciones());
    $request->setContainer(app())->setRedirector(app('redirect'));
    $request->validateResolved();

    return $request;
}

it('acepta las tres fechas permitidas', function (string $fecha) {
    $request = peticionDeLayout("estaciones[]=1649&fecha_vigencia={$fecha}");

    expect($request->fechaVigencia())->toBe($fecha);
})->with([
    fn () => Carbon::yesterday()->toDateString(),
    fn () => Carbon::today()->toDateString(),
    fn () => Carbon::tomorrow()->toDateString(),
]);

it('rechaza cualquier otra fecha de vigencia', function (string $fecha) {
    peticionDeLayout("estaciones[]=1649&fecha_vigencia={$fecha}");
})->with([
    fn () => Carbon::today()->addDays(2)->toDateString(),
    fn () => Carbon::today()->subDays(2)->toDateString(),
    '2026-13-45',
    'ayer',
    "2026-08-31' OR 1=1",
])->throws(ValidationException::class);

it('usa hoy cuando no se manda fecha', function () {
    expect(peticionDeLayout('estaciones[]=1649')->fechaVigencia())
        ->toBe(Carbon::today()->toDateString());
});

it('colapsa las estaciones repetidas', function () {
    $request = peticionDeLayout('estaciones[]=1649&estaciones[]=1649&estaciones[]=200&estaciones[]=1649');

    expect($request->estaciones())->toBe([1649, 200]);
});

it('rechaza estaciones que no son del usuario', function () {
    peticionDeLayout('estaciones[]=1649&estaciones[]=99999');
})->throws(ValidationException::class);

it('exige al menos una estación', function () {
    peticionDeLayout('estaciones[]=');
})->throws(ValidationException::class);

it('pide una sola vez cada estación y comparte el path base', function () {
    config()->set('services.maosa_api.pemex_base_url', 'https://dev.maosasolutions.com');
    config()->set('services.maosa_api.pemex_layout_path', '/precio_pemex/layout-api/estacion');
    config()->set('services.maosa_api.token', 'token-de-prueba');

    Http::fake(['*' => Http::response('OK', 200)]);

    $respuestas = (new PrecioPemexApiService())
        ->layouts([1649, 1649, 200], PrecioPemexApiService::FORMATO_IMAGEN, '2026-08-31');

    expect(array_keys($respuestas))->toBe([1649, 200]);

    Http::assertSentCount(2);
    Http::assertSent(fn ($request) => $request->url()
        === 'https://dev.maosasolutions.com/precio_pemex/layout-api/estacion/1649/imagen?fecha_vigencia=2026-08-31');
    Http::assertSent(fn ($request) => $request->url()
        === 'https://dev.maosasolutions.com/precio_pemex/layout-api/estacion/200/imagen?fecha_vigencia=2026-08-31');
});

it('arma los cuatro formatos sobre el mismo path base', function (string $formato) {
    config()->set('services.maosa_api.pemex_base_url', 'https://dev.maosasolutions.com');
    config()->set('services.maosa_api.pemex_layout_path', '/precio_pemex/layout-api/estacion');

    Http::fake(['*' => Http::response('OK', 200)]);

    (new PrecioPemexApiService())->layout(1649, $formato, '2026-08-31');

    Http::assertSent(fn ($request) => $request->url()
        === "https://dev.maosasolutions.com/precio_pemex/layout-api/estacion/1649/{$formato}?fecha_vigencia=2026-08-31");
})->with([
    PrecioPemexApiService::FORMATO_HTML,
    PrecioPemexApiService::FORMATO_EXCEL,
    PrecioPemexApiService::FORMATO_PDF,
    PrecioPemexApiService::FORMATO_IMAGEN,
]);
