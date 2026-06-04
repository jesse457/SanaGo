<?php

namespace App\Enums;

enum AdmissionStatus: string
{
    case Admitted = 'admitted';
    case Discharged = 'discharged';
    case Transferred = 'transferred';
    case Pending = 'pending';
}
