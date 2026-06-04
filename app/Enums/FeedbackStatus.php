<?php

namespace App\Enums;

enum FeedbackStatus: string
{
    case PENDING = 'pending';
    case ADDRESSED = 'addressed';
    case DISMISSED = 'dismissed';
}
