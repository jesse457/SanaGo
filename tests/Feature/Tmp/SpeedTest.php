<?php
use Illuminate\Support\Facades\Route;

it('times route variants', function () {
    Route::middleware('web')->get('/__speed-json', fn () => response()->json(['ok' => true]));
    Route::middleware('web')->get('/__speed-inertia', fn () => Inertia\Inertia::render('Home'));

    foreach (['/__speed-json', '/__speed-inertia', '/does-not-exist-xyz', '/login'] as $url) {
        $t = microtime(true);
        try { $r = $this->get($url); fwrite(STDERR, "\n$url -> ".$r->getStatusCode()); } catch (Throwable $e) { fwrite(STDERR, "\n$url ERR: ".substr($e->getMessage(),0,50)); }
        fwrite(STDERR, " took: ".round(microtime(true)-$t,2)."s");
    }
    expect(true)->toBeTrue();
});
