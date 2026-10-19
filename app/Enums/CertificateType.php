<?php

namespace App\Enums;

enum CertificateType: string
{
    case SickLeave = 'sick_leave';
    case MedicalFitness = 'medical_fitness';
    case Death = 'death';
}
