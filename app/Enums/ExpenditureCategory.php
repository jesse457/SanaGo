<?php

namespace App\Enums;

enum ExpenditureCategory: string
{
    case SALARIES = 'salaries';
    case SUPPLIES = 'supplies';
    case UTILITIES = 'utilities';
    case MAINTENANCE = 'maintenance';
    case EQUIPMENT = 'equipment';
    case PHARMACEUTICALS = 'pharmaceuticals';
    case ADMINISTRATIVE = 'administrative';
    case OTHER = 'other';
}
