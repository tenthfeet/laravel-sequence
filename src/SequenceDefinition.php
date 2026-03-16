<?php

namespace Tenthfeet\Sequence;

use Carbon\Carbon;
use Carbon\Month;
use Illuminate\Database\Eloquent\Model;
use Tenthfeet\Sequence\Enums\ResetPolicy;

abstract class SequenceDefinition
{
    protected ?string $pattern = null;
    protected ?string $paddingCharacter = null;
    protected ?ResetPolicy $resetPolicy = null;
    protected ?Model $model = null;

    protected ?Month $financialYearStartMonth = null;
    protected ?Carbon $explicitDate = null;

    abstract public function key(): string;

    /* ---------------- Fluent API ---------------- */

    public function usingDate(Carbon $date): self
    {
        $this->explicitDate = $date;
        return $this;
    }

    public function pattern(string $pattern): self
    {
        $this->pattern = $pattern;
        return $this;
    }

    public function padWith(string $char): self
    {
        $this->paddingCharacter = $char;
        return $this;
    }

    public function resetPolicy(ResetPolicy $policy): self
    {
        $this->resetPolicy = $policy;
        return $this;
    }

    public function financialYearStartsIn(Month $month): self
    {
        $this->financialYearStartMonth = $month;
        return $this;
    }

    public function forModel(Model $model): self
    {
        $this->model = $model;
        return $this;
    }

    /* ---------------- Getters ---------------- */

    final public function getKey(): string
    {
        return $this->key();
    }

    public function getPattern(): string
    {
        return $this->pattern ?? config('sequence.default_pattern', '{SEQ:3}');
    }

    public function getPaddingCharacter(): string
    {
        return $this->paddingCharacter ?? config('sequence.default_padding_character', '0');
    }

    public function getModel(): ?Model
    {
        return $this->model;
    }

    public function getResetPolicy(): ResetPolicy
    {
        return $this->resetPolicy ?? config('sequence.default_reset_policy', ResetPolicy::None);
    }

    public function getFinancialYearStartMonth(): Month
    {
        return $this->financialYearStartMonth ?? config('sequence.financial_year.start_month', Month::April);
    }

    /**
     * Canonical reset scope (DB-safe).
     */
    public function getResetPolicyValue(): ?string
    {
        return $this->getResetPolicy()->resetValue(
            $this->resolveSequenceDate(),
            $this->getFinancialYearStartMonth()
        );
    }

    /* ---------------- Date Resolution ---------------- */

    final public function resolveSequenceDate(): Carbon
    {
        return $this->explicitDate ?? $this->sequenceDate() ?? Carbon::now();
    }

    protected function sequenceDate(): ?Carbon
    {
        return null;
    }
}
