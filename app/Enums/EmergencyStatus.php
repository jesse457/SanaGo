<?php

namespace App\Enums;

enum EmergencyStatus: string
{
    case TRIAGE = 'triage';
    case ACTIVE = 'active';
    case STABILIZED = 'stabilized';
    case ADMITTED = 'admitted';
    case DISCHARGED = 'discharged';
    case DECEASED = 'deceased';
}
