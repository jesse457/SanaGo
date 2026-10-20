<?php

namespace App\Enums;

enum SubscriptionStatus: string
{
    case TRIALING = 'trialing';
    case ACTIVE = 'active';
    case PAST_DUE = 'past_due';
    case EXPIRED = 'expired';
    case CANCELLED = 'cancelled';
    case INACTIVE = 'inactive';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Statuses that grant access to the tenant application.
     *
     * @return array<int, self>
     */
    public static function usable(): array
    {
        return [self::TRIALING, self::ACTIVE, self::PAST_DUE];
    }

    public function grantsAccess(): bool
    {
        return in_array($this, self::usable(), true);
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::EXPIRED, self::CANCELLED, self::INACTIVE], true);
    }

    public function label(): string
    {
        return match ($this) {
            self::TRIALING => 'Trialing',
            self::ACTIVE => 'Active',
            self::PAST_DUE => 'Past due',
            self::EXPIRED => 'Expired',
            self::CANCELLED => 'Cancelled',
            self::INACTIVE => 'Inactive',
        };
    }

    /**
     * Semantic tone used by the UI badge component.
     */
    public function tone(): string
    {
        return match ($this) {
            self::TRIALING => 'info',
            self::ACTIVE => 'positive',
            self::PAST_DUE => 'warning',
            self::EXPIRED, self::CANCELLED => 'critical',
            self::INACTIVE => 'neutral',
        };
    }
}
