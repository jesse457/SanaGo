<?php

namespace App\Enums;

enum UrgencyLevel: string
{
    case NORMAL = 'normal';
    case URGENT = 'urgent';
    case EMERGENCY = 'emergency';
}
