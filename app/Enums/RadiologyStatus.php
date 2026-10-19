<?php

namespace App\Enums;

enum RadiologyStatus: string
{
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Validated = 'validated';
}
