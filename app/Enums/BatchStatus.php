<?php

namespace App\Enums;

enum BatchStatus: string
{
    case EXPIRED = 'expired';
    case ACTIVE = 'active';
    case DEPLETED = 'depleted';
}
