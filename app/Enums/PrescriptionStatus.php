<?php

namespace App\Enums;

enum PrescriptionStatus: string
{
    case NEW = 'new';
    case PARTIAL = 'partial';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
}
