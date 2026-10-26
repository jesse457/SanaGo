<?php

declare(strict_types=1);

use App\Services\Laboratory\Analyzer\AstmParser;
use App\Services\Laboratory\Analyzer\CsvParser;
use App\Services\Laboratory\Analyzer\Hl7Parser;

/*
|--------------------------------------------------------------------------
| Analyzer readers
|--------------------------------------------------------------------------
| The frames below are shaped like the ones the analyzers in service in
| Cameroonian hospital labs actually emit: ASTM E1394 from a Mindray
| haematology bench (serial/USB), HL7 ORU^R01 from a networked instrument, and
| a semicolon CSV export from analyzer PC software.
*/

function astmFrame(): string
{
    return implode("\r", [
        'H|\\^&|||Mindray^BC-5150^V2.1|||||||P|1|20261026101500',
        'P|1||CM-0098123||MBALLA^Jean^Paul||19850312|M',
        'O|1|ACC-20261026-0007||^^^CBC|R||||||||1',
        'R|1|^^^WBC^White blood cells|6.8|10^3/uL|4.0-11.0|N||F||||20261026101612',
        'R|2|^^^HGB^Haemoglobin|9.4|g/dL|11.5-16.5|LL||F||||20261026101612',
        'R|3|^^^PLT^Platelets|210|10^3/uL|150-450|||||F||||20261026101612',
        'L|1|N',
    ])."\r";
}

function hl7Frame(): string
{
    return implode("\r", [
        'MSH|^~\\&|BC-5150|LAB|SanaGo|HOSPITAL|20261026101500||ORU^R01|MSG00012|P|2.3.1',
        'PID|1||CM-0098123||MBALLA^Jean^Paul||19850312|M',
        'OBR|1||ACC-20261026-0007|CBC^Full blood count|||20261026101500',
        'OBX|1|NM|WBC^White blood cells^L||6.8|10^3/uL|4.0-11.0|N|||F',
        'OBX|2|NM|HGB^Haemoglobin^L||9.4|g/dL|11.5-16.5|LL|||F',
    ]);
}

it('reads an ASTM frame from a hematology analyzer', function () {
    $parsed = (new AstmParser)->parse(astmFrame());

    expect($parsed->failed())->toBeFalse()
        ->and($parsed->format)->toBe('astm')
        ->and($parsed->analyzerName)->toBe('Mindray BC-5150')
        ->and($parsed->analyzerSerial)->toBe('V2.1')
        ->and($parsed->sampleId)->toBe('ACC-20261026-0007')
        ->and($parsed->patientId)->toBe('CM-0098123')
        ->and($parsed->patientName)->toBe('Jean MBALLA')
        ->and($parsed->measuredAt)->toBe('2026-10-26 10:16:12')
        ->and($parsed->results)->toHaveCount(3);
});

it('normalises ASTM analyte flags and keeps the reference range', function () {
    $parsed = (new AstmParser)->parse(astmFrame());

    [$wbc, $hgb, $plt] = $parsed->results;

    expect($wbc->code)->toBe('WBC')
        ->and($wbc->name)->toBe('White blood cells')
        ->and($wbc->value)->toBe('6.8')
        ->and($wbc->unit)->toBe('10^3/uL')
        ->and($wbc->flag)->toBe('N')
        ->and($hgb->flag)->toBe('LL')
        ->and($hgb->isCritical())->toBeTrue()
        ->and($plt->flag)->toBe('N')
        ->and($parsed->hasCriticalResult())->toBeTrue();
});

it('formats an analyte the way the result grid parses it back', function () {
    $parsed = (new AstmParser)->parse(astmFrame());

    expect($parsed->results[1]->toResultLine())
        ->toBe('Haemoglobin: 9.4 g/dL (11.5-16.5)');
});

it('reads an HL7 ORU result message', function () {
    $parsed = (new Hl7Parser)->parse(hl7Frame());

    expect($parsed->failed())->toBeFalse()
        ->and($parsed->format)->toBe('hl7')
        ->and($parsed->analyzerName)->toBe('BC-5150')
        ->and($parsed->sampleId)->toBe('ACC-20261026-0007')
        ->and($parsed->patientId)->toBe('CM-0098123')
        ->and($parsed->patientName)->toBe('Jean MBALLA')
        ->and($parsed->results)->toHaveCount(2)
        ->and($parsed->results[0]->code)->toBe('WBC')
        ->and($parsed->results[0]->referenceRange)->toBe('4.0-11.0')
        ->and($parsed->results[1]->flag)->toBe('LL');
});

it('reads an HL7 message wrapped in MLLP framing', function () {
    $framed = "\x0b".hl7Frame()."\x1c\r";

    $parsed = (new Hl7Parser)->parse($framed);

    expect($parsed->results)->toHaveCount(2)
        ->and($parsed->sampleId)->toBe('ACC-20261026-0007');
});

it('reads a semicolon CSV export from analyzer software', function () {
    $csv = implode("\n", [
        'Sample ID;Test Code;Test Name;Value;Unit;Reference Range;Flag;Measured At',
        'ACC-20261026-0007;GLU;Glucose;6.4;mmol/L;3.9-5.5;H;2026-10-26 10:12',
        'ACC-20261026-0007;CREA;Creatinine;88;umol/L;62-106;N;2026-10-26 10:12',
    ]);

    $parsed = (new CsvParser)->parse($csv);

    expect($parsed->failed())->toBeFalse()
        ->and($parsed->format)->toBe('csv')
        ->and($parsed->sampleId)->toBe('ACC-20261026-0007')
        ->and($parsed->results)->toHaveCount(2)
        ->and($parsed->results[0]->code)->toBe('GLU')
        ->and($parsed->results[0]->value)->toBe('6.4')
        ->and($parsed->results[0]->flag)->toBe('H')
        ->and($parsed->results[1]->flag)->toBe('N');
});

it('reports a frame that carries no results instead of inventing them', function () {
    $parsed = (new AstmParser)->parse("H|\\^&|||Mindray^BC-5150\rP|1||CM-1\rL|1|N\r");

    expect($parsed->failed())->toBeTrue()
        ->and($parsed->results)->toBe([])
        ->and($parsed->error)->toContain('no result');
});

it('ignores a blank analyte value but keeps a real zero', function () {
    $frame = implode("\r", [
        'H|\\^&|||Mindray^BC-5150',
        'O|1|ACC-1',
        'R|1|^^^WBC^White blood cells||10^3/uL|4.0-11.0|N',
        'R|2|^^^K^Potassium|0|mmol/L|3.5-5.1|L',
    ]);

    $parsed = (new AstmParser)->parse($frame);

    expect($parsed->results)->toHaveCount(1)
        ->and($parsed->results[0]->code)->toBe('K')
        ->and($parsed->results[0]->value)->toBe('0');
});
