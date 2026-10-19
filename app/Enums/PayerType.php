<?php

namespace App\Enums;

enum PayerType: string
{
    case Government = 'government';
    case Insurance = 'insurance';
    case Mutual = 'mutual';
    case Employer = 'employer';
}
