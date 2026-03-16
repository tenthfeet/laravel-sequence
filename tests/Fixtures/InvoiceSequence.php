<?php

namespace Tenthfeet\Sequence\Tests\Fixtures;

use Carbon\Carbon;
use Carbon\Month;
use Tenthfeet\Sequence\Enums\ResetPolicy;
use Tenthfeet\Sequence\SequenceDefinition;

final class InvoiceSequence extends SequenceDefinition
{
    public function __construct(?Carbon $date = null)
    {
        if ($date) {
            $this->usingDate($date);
        }

        $this->resetPolicy(ResetPolicy::FinancialYear)
             ->pattern('{FY:YYYY-YY}/{SEQ:4}')
             ->financialYearStartsIn(Month::April);
    }

    public function key(): string
    {
        return 'invoice';
    }
}
