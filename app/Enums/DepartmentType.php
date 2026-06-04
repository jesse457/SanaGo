<?php

namespace App\Enums;

enum DepartmentType: string
{
    case CLINICAL = 'clinical';
    case ADMINISTRATIVE = 'administrative';
    case SUPPORT = 'support';
}
