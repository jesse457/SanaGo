<?php

namespace App\Enums;

enum SubscriptionInvoiceStatus: string
{
    case DRAFT = 'draft';
    case PENDING = 'pending';
    case PAID = 'paid';
    case OVERDUE = 'overdue';
    case VOID = 'void';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function tone(): string
    {
        return match ($this) {
            self::PAID => 'positive',
            self::PENDING => 'info',
            self::OVERDUE => 'warning',
            self::VOID => 'neutral',
            self::DRAFT => 'neutral',
        };
    }

    public function isOutstanding(): bool
    {
        return in_array($this, [self::PENDING, self::OVERDUE], true);
    }
}
