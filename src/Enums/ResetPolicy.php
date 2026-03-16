<?php

namespace Tenthfeet\Sequence\Enums;

use Carbon\Carbon;
use Carbon\Month;

enum ResetPolicy: string
{
    case None = 'none';
    case Yearly = 'yearly';
    case Monthly = 'monthly';
    case Daily = 'daily';
    case FinancialYear = 'financial_year';

    /**
     * Canonical reset value (used for DB grouping & locking).
     */
    public function resetValue(
        Carbon $date,
        Month $financialYearStartMonth
    ): ?string {
        return match ($this) {
            self::Yearly => $date->format('Y'),
            self::Monthly => $date->format('Y-m'),
            self::Daily => $date->format('Y-m-d'),
            self::FinancialYear => $this->financialYearKey(
                $date,
                $financialYearStartMonth
            ),
            self::None => null,
        };
    }

    /**
     * Canonical FY key (ALWAYS YYYY-YYYY).
     */
    private function financialYearKey(
        Carbon $date,
        Month $startMonth
    ): string {
        $startYear = $date->month >= $startMonth->value
            ? $date->year
            : $date->year - 1;

        return "{$startYear}-" . ($startYear + 1);
    }
}
