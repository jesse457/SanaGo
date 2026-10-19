<?php

namespace App\Enums;

enum CertificateStatus: string
{
    case Issued = 'issued';
    case Cancelled = 'cancelled';
    case Voided = 'voided';
}
