<?php

namespace App\Enums;

/**
 * Payment state of one invoice line.
 *
 * Payment state belongs to the charge, never to the clinical record.
 */
enum PaymentStatus: string
{
    case Pending = 'pending';
    case PartiallyPaid = 'partially_paid';
    case Paid = 'paid';
    case Waived = 'waived';
    case Refunded = 'refunded';
}
