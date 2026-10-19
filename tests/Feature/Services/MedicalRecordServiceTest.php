<?php

declare(strict_types=1);

use App\Services\Clinical\MedicalRecordService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function callProtected(object $object, string $method, array $args = []): mixed
{
    $reflection = new ReflectionClass($object);

    return $reflection->getMethod($method)->invokeArgs($object, $args);
}

it('flags temperature below configured minimum as abnormal', function () {
    config(['clinical.vitals.temperature.min' => 36.0]);

    $service = app(MedicalRecordService::class);
    $result = callProtected($service, 'flagAbnormalVitals', [['temperature' => 35.5]]);

    expect($result)->toBeTrue();
});

it('flags temperature above configured maximum as abnormal', function () {
    config(['clinical.vitals.temperature.max' => 38.0]);

    $service = app(MedicalRecordService::class);
    $result = callProtected($service, 'flagAbnormalVitals', [['temperature' => 38.5]]);

    expect($result)->toBeTrue();
});

it('does not flag normal temperature', function () {
    config(['clinical.vitals.temperature.min' => 36.0, 'clinical.vitals.temperature.max' => 38.0]);

    $service = app(MedicalRecordService::class);
    $result = callProtected($service, 'flagAbnormalVitals', [['temperature' => 37.0]]);

    expect($result)->toBeFalse();
});

it('does not flag empty vitals', function () {
    $service = app(MedicalRecordService::class);
    $result = callProtected($service, 'flagAbnormalVitals', [[]]);

    expect($result)->toBeFalse();
});

it('flags low oxygen saturation based on config', function () {
    config(['clinical.vitals.oxygen_saturation.min' => 95]);

    $service = app(MedicalRecordService::class);
    $result = callProtected($service, 'flagAbnormalVitals', [['oxygen_saturation' => 91]]);

    expect($result)->toBeTrue();
});

it('flags high heart rate based on config', function () {
    config(['clinical.vitals.heart_rate.max' => 100]);

    $service = app(MedicalRecordService::class);
    $result = callProtected($service, 'flagAbnormalVitals', [['heart_rate' => 110]]);

    expect($result)->toBeTrue();
});
