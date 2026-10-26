<?php

declare(strict_types=1);

/**
 * The identity ships in two forms: a set of static assets a crawler, an iOS
 * home screen or an email can read, and a React component every shell mounts.
 * Both draw the same artwork. These tests hold the two in step: a logo that
 * drifts between the favicon and the header is worse than no logo.
 */
function brandMarkPaths(string $contents): array
{
    // The path data lives in an SVG attribute in the assets and in a quoted
    // constant in the component, so accept both spellings.
    preg_match_all('/(?:d="|\x27)(M[\d. -][^"\x27]{500,})/', $contents, $matches);

    return $matches[1];
}

/** Width and height straight out of the PNG IHDR chunk. */
function brandPngSize(string $path): array
{
    $bytes = file_get_contents($path);
    $unpacked = unpack('Nwidth/Nheight', substr($bytes, 16, 8));

    return [$unpacked['width'], $unpacked['height']];
}

it('ships a favicon that carries the SanaGo mark', function () {
    $svg = File::get(public_path('favicon.svg'));

    expect($svg)
        ->toContain('viewBox="0 0 439 392"')
        // Both inks of the identity are present: the figure's blue and the
        // care half's green.
        ->toContain('#0074bc')
        ->toContain('#40b448')
        ->and(brandMarkPaths($svg))->toHaveCount(2);

    // The legacy Laravel mark must not linger in the .ico fallback.
    expect(strlen(File::get(public_path('favicon.ico'))))->toBeGreaterThan(1000);

    $header = substr(File::get(public_path('favicon.ico')), 0, 4);

    expect(bin2hex($header))->toBe('00000100');
});

it('ships a 180px apple touch icon', function () {
    $path = public_path('apple-touch-icon.png');

    expect(File::exists($path))->toBeTrue()
        ->and(brandPngSize($path))->toBe([180, 180]);
});

it('ships a 1200x630 social card', function () {
    $path = public_path('images/marketing/og-image.png');

    expect(File::exists($path))->toBeTrue()
        ->and(brandPngSize($path))->toBe([1200, 630]);
});

it('keeps the static mark identical to the React glyph', function () {
    $asset = brandMarkPaths(File::get(public_path('brand/mark.svg')));
    $component = brandMarkPaths(File::get(resource_path('js/Components/brand/SanaGoLogo.tsx')));

    expect($asset)->toHaveCount(2)
        ->and($component)->toHaveCount(2);

    // Same two paths, same order: the figure first, then the care half.
    expect($component)->toBe($asset);
});

it('pairs the mark with wordmark inks that exist as theme tokens', function () {
    $css = File::get(resource_path('css/app.css'));
    $component = File::get(resource_path('js/Components/brand/SanaGoLogo.tsx'));

    foreach (['brand-figure', 'brand-care', 'brand-figure-ink', 'brand-care-ink'] as $token) {
        expect($css)->toContain("--color-{$token}: var(--sg-{$token})");
        expect($css)->toContain("--sg-{$token}:");
    }

    expect($component)
        ->toContain('text-brand-figure-ink')
        ->toContain('text-brand-care-ink');
});

it('advertises the social card from the marketing layout', function () {
    $layout = File::get(resource_path('js/Layouts/PublicLayout.tsx'));

    expect($layout)->toContain('/images/marketing/og-image.png');

    // Every image the marketing layout references must exist on disk.
    preg_match_all('#/images/marketing/[A-Za-z0-9._-]+#', $layout, $matches);

    expect($matches[0])->not->toBeEmpty();

    foreach (array_unique($matches[0]) as $image) {
        expect(File::exists(public_path(ltrim($image, '/'))))->toBeTrue();
    }
});
