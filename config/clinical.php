<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Vital Signs Normal Ranges
    |--------------------------------------------------------------------------
    |
    | These thresholds are used by MedicalRecordService::flagAbnormalVitals()
    | to determine whether a patient's vital signs are outside normal range.
    |
    */
    'vitals' => [
        'temperature' => [
            'min' => (float) env('CLINICAL_TEMP_MIN', 36.1),
            'max' => (float) env('CLINICAL_TEMP_MAX', 37.8),
        ],
        'systolic_bp' => [
            'min' => (int) env('CLINICAL_SYSTOLIC_MIN', 90),
            'max' => (int) env('CLINICAL_SYSTOLIC_MAX', 140),
        ],
        'diastolic_bp' => [
            'min' => (int) env('CLINICAL_DIASTOLIC_MIN', 60),
            'max' => (int) env('CLINICAL_DIASTOLIC_MAX', 90),
        ],
        'heart_rate' => [
            'min' => (int) env('CLINICAL_HR_MIN', 60),
            'max' => (int) env('CLINICAL_HR_MAX', 100),
        ],
        'oxygen_saturation' => [
            'min' => (float) env('CLINICAL_SPO2_MIN', 95),
        ],
        'respiratory_rate' => [
            'min' => (int) env('CLINICAL_RR_MIN', 12),
            'max' => (int) env('CLINICAL_RR_MAX', 20),
        ],
    ],

];
