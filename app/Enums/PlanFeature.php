<?php

namespace App\Enums;

/**
 * Capabilities a plan can grant a tenant.
 *
 * These keys are stored in `plans.feature_flags_json` and may be overridden
 * per tenant in `subscriptions.feature_overrides_json`. They gate modules in
 * the tenant app (navigation + routes) independently of RBAC permissions.
 */
enum PlanFeature: string
{
    case Patients = 'patients';
    case Appointments = 'appointments';
    case Consultations = 'consultations';
    case Admissions = 'admissions';
    case Vitals = 'vitals';
    case Laboratory = 'laboratory';
    case Radiology = 'radiology';
    case Pharmacy = 'pharmacy';
    case Inventory = 'inventory';
    case Surgery = 'surgery';
    case Emergency = 'emergency';
    case Billing = 'billing';
    case CashRegister = 'cash_register';
    case InsuranceClaims = 'insurance_claims';
    case Certificates = 'certificates';
    case Reports = 'reports';
    case AuditLogs = 'audit_logs';
    case SmsNotifications = 'sms_notifications';
    case ApiAccess = 'api_access';
    case CustomBranding = 'custom_branding';

    public function label(): string
    {
        return match ($this) {
            self::Patients => 'Patient records',
            self::Appointments => 'Appointments',
            self::Consultations => 'Consultations & medical records',
            self::Admissions => 'Admissions & wards',
            self::Vitals => 'Vitals & nursing care',
            self::Laboratory => 'Laboratory',
            self::Radiology => 'Radiology & imaging',
            self::Pharmacy => 'Pharmacy',
            self::Inventory => 'Inventory & supplies',
            self::Surgery => 'Surgery',
            self::Emergency => 'Emergency',
            self::Billing => 'Billing & invoicing',
            self::CashRegister => 'Cash register (Caisse)',
            self::InsuranceClaims => 'Insurance & tiers payant',
            self::Certificates => 'Medical certificates',
            self::Reports => 'Reports & analytics',
            self::AuditLogs => 'Audit logs',
            self::SmsNotifications => 'SMS notifications',
            self::ApiAccess => 'API access',
            self::CustomBranding => 'Custom branding',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Patients, self::Appointments, self::Consultations => 'Core clinical workflow.',
            self::Laboratory, self::Radiology => 'Diagnostics module.',
            self::Pharmacy, self::Inventory => 'Stock and dispensing.',
            self::Billing, self::CashRegister, self::InsuranceClaims => 'Money handling.',
            self::SmsNotifications => 'SMS alerts for patients (MTN / Orange).',
            self::ApiAccess => 'Machine-to-machine integration.',
            default => 'Module access.',
        };
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * @return array<int, array{value: string, label: string, description: string}>
     */
    public static function options(): array
    {
        return array_map(fn (self $feature) => [
            'value' => $feature->value,
            'label' => $feature->label(),
            'description' => $feature->description(),
        ], self::cases());
    }

    /**
     * The smallest sensible feature set — available on every plan.
     *
     * @return array<int, string>
     */
    public static function core(): array
    {
        return [
            self::Patients->value,
            self::Appointments->value,
            self::Consultations->value,
            self::Billing->value,
        ];
    }

    /**
     * Feature flags for a seeded plan, keyed by plan slug.
     *
     * @return array<string, bool>
     */
    public static function forPlanSlug(string $slug): array
    {
        $basic = array_fill_keys(self::core(), true) + [
            self::Admissions->value => true,
            self::Vitals->value => true,
            self::Laboratory->value => true,
            self::Pharmacy->value => true,
            self::Inventory->value => true,
            self::Certificates->value => true,
            self::Reports->value => true,
        ];

        $professional = $basic + [
            self::Radiology->value => true,
            self::Surgery->value => true,
            self::Emergency->value => true,
            self::CashRegister->value => true,
            self::InsuranceClaims->value => true,
            self::AuditLogs->value => true,
            self::SmsNotifications->value => true,
        ];

        $enterprise = $professional + [
            self::ApiAccess->value => true,
            self::CustomBranding->value => true,
        ];

        return match ($slug) {
            'enterprise' => $enterprise,
            'professional' => $professional,
            default => $basic,
        };
    }
}
