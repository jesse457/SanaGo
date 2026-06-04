<?php

namespace App\Enums;

enum BedStatus: string
{
    case Available = 'available';
    case Occupied = 'occupied';
    case Maintenance = 'maintenance';
}
