<?php

declare(strict_types=1);

it('demonstrates the Pest expectations API', function () {
    expect(true)->toBeTrue();
    expect(false)->toBeFalse();
    expect(42)->toBeInt()->toBeGreaterThan(40);
});

it('demonstrates string expectations', function () {
    $name = 'SanaGo';

    expect($name)
        ->toBeString()
        ->toContain('Sana')
        ->toEndWith('Go');
});

it('demonstrates array expectations', function () {
    $modules = ['Patients', 'Appointments', 'Lab', 'Pharmacy'];

    expect($modules)
        ->toBeArray()
        ->toHaveCount(4)
        ->toContain('Pharmacy');
});
