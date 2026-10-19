<?php

namespace App\Enums;

enum ServiceCategory: string
{
    case Consultation = 'consultation';
    case Procedure = 'procedure';
    case Lab = 'lab';
    case Radiology = 'radiology';
    case Imaging = 'imaging';
    case Medication = 'medication';
    case Bed = 'bed';
    case Nursing = 'nursing';
    case Emergency = 'emergency';
    case Other = 'other';
}
