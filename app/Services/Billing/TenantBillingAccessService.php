<?php

declare(strict_types=1);

namespace App\Services\Billing;

use App\Models\Tenant;
use Carbon\Carbon;

class TenantBillingAccessService
{
    private const OVERDUE_GRACE_DAYS = 30;

    /**
     * Tenant is suspended when billing is in delinquent status for more than 30 days.
     */
    public function suspensionSnapshot(Tenant $tenant): array
    {
        $billingStatus = strtolower((string) ($tenant->billing_status ?? 'inactive'));
        $delinquentStatuses = ['past_due', 'unpaid', 'uncollectible', 'canceled', 'incomplete_expired'];

        if (!in_array($billingStatus, $delinquentStatuses, true)) {
            return $this->baseSnapshot(false, 0, null, $billingStatus);
        }

        $referenceDate = $this->referenceDate($tenant);
        if (!$referenceDate) {
            return $this->baseSnapshot(false, 0, null, $billingStatus);
        }

        $overdueDays = (int) $referenceDate->diffInDays(now());
        $isSuspended = $referenceDate->lte(now()->subDays(self::OVERDUE_GRACE_DAYS));

        return $this->baseSnapshot($isSuspended, $overdueDays, $referenceDate->toISOString(), $billingStatus);
    }

    public function isSuspended(Tenant $tenant): bool
    {
        return (bool) ($this->suspensionSnapshot($tenant)['is_suspended'] ?? false);
    }

    private function referenceDate(Tenant $tenant): ?Carbon
    {
        $rawReference = $tenant->billing_last_invoice_at
            ?? $tenant->billing_current_period_end
            ?? $tenant->updated_at;

        if (!$rawReference) {
            return null;
        }

        return $rawReference instanceof Carbon
            ? $rawReference
            : Carbon::parse((string) $rawReference);
    }

    private function baseSnapshot(bool $isSuspended, int $overdueDays, ?string $referenceDate, string $billingStatus): array
    {
        return [
            'is_suspended' => $isSuspended,
            'overdue_days' => $overdueDays,
            'grace_days' => self::OVERDUE_GRACE_DAYS,
            'reference_date' => $referenceDate,
            'status_trigger' => $billingStatus,
            'reason' => $isSuspended
                ? 'Tenant suspendido temporalmente por impago superior a 30 días.'
                : null,
        ];
    }
}
