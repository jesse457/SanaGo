<?php

namespace App\Enums;

enum ShiftType: string
{
    case MORNING = 'morning';
    case AFTERNOON = 'afternoon';
    case NIGHT = 'night';
}
