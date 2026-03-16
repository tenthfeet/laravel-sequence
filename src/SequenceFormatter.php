<?php

namespace Tenthfeet\Sequence;

use Carbon\Carbon;
use InvalidArgumentException;

final class SequenceFormatter
{
    public function __construct(
        private readonly SequenceDefinition $definition
    ) {}

    public function format(int $counter): string
    {
        $pattern = $this->definition->getPattern();
        $date    = $this->definition->resolveSequenceDate();

        $formatted = str_replace(
            array_keys($this->dateTokens($date)),
            array_values($this->dateTokens($date)),
            $pattern
        );

        $formatted = $this->replaceFinancialYear($formatted);
        $formatted = $this->replaceSequence($formatted, $counter);

        return $formatted;
    }

    private function dateTokens(Carbon $date): array
    {
        return [
            '{YYYY}' => $date->format('Y'),
            '{YY}' => $date->format('y'),
            '{MM}' => $date->format('m'),
            '{DD}' => $date->format('d'),
            '{H}' => $date->format('H'),
            '{M}' => $date->format('i'),
            '{S}' => $date->format('s'),
        ];
    }

    private function replaceFinancialYear(string $value): string
    {
        if (!preg_match('/\{FY(?::([A-Z\-]+))?\}/', $value, $m)) {
            return $value;
        }

        $resetScope = $this->definition->getResetPolicyValue();

        if ($resetScope === null) {
            throw new InvalidArgumentException('FY token used without FY reset policy.');
        }

        [$start, $end] = explode('-', $resetScope);
        $format = $m[1] ?? 'YYYY-YY';

        $fy = match ($format) {
            'YYYY-YYYY' => "{$start}-{$end}",
            'YYYY-YY' => "{$start}-" . substr($end, -2),
            'YY-YY' => substr($start, -2) . '-' . substr($end, -2),
            default => throw new InvalidArgumentException("Invalid FY format {$format}")
        };

        return str_replace($m[0], $fy, $value);
    }

    private function replaceSequence(string $value, int $counter): string
    {
        if (preg_match('/\{SEQ:(\d+)\}/', $value, $m)) {
            return str_replace(
                $m[0],
                str_pad(
                    $counter,
                    (int) $m[1],
                    $this->definition->getPaddingCharacter(),
                    STR_PAD_LEFT
                ),
                $value
            );
        }

        return str_replace('{SEQ}', (string) $counter, $value);
    }
}
