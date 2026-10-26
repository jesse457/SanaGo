<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Analyzer ingestion
    |--------------------------------------------------------------------------
    |
    | Cameroonian hospital labs usually connect a bench analyzer over a serial
    | (USB) port with ASTM E1394, and bridge that to HTTP with a small helper on
    | the analyzer's PC. `token` is the shared secret that helper — or an
    | analyzer with a direct TCP/HL7 interface — sends as `Authorization:
    | Bearer <token>` to POST /lab/analyzer/ingest.
    |
    | Leave it empty to switch the machine endpoint off: only signed-in staff
    | can import then, through the result entry screen.
    |
    */

    'token' => env('LAB_ANALYZER_TOKEN'),

    /*
    | A frame that matches an open order completes that order. Turn this off in
    | labs where a supervisor must verify every machine result before it is
    | filed; the result is still written, just not marked completed.
    */

    'auto_complete_request' => env('LAB_ANALYZER_AUTO_COMPLETE', true),

    /*
    | The largest raw frame accepted, in kilobytes. A full CBC run is a few
    | kilobytes; the cap only stops a runaway bridge from filling the table.
    */

    'max_payload_kb' => (int) env('LAB_ANALYZER_MAX_PAYLOAD_KB', 256),

];
