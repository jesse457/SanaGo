<?php

namespace App\Enums;

enum LabResultStatus: string
{
    case NORMAL = 'normal';
    case ABNORMAL = 'abnormal';
    case CRITICAL = 'critical';
}
