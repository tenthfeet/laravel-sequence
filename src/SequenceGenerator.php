<?php

namespace Tenthfeet\Sequence;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use InvalidArgumentException;
use Tenthfeet\Sequence\Enums\ResetPolicy;
use Tenthfeet\Sequence\Models\Sequence;

abstract class SequenceGenerator
{
    /**
     * Unique key used for the sequence (e.g., 'invoice', 'order').
     */
    protected ?string $key = null;

    /**
     * Character used to pad the sequence number.
     */
    protected ?string $paddingCharacter = null;

    /**
     * Pattern defining the sequence format (e.g., "{YYYY}-{SEQ:4}").
     */
    protected ?string $pattern = null;

    /**
     * Reset policy determining when the sequence counter resets.
     */
    protected ?ResetPolicy $resetPolicy = null;

    /**
     * @param  Model|null  $model  Optional model associated with the sequence.
     */
    public function __construct(protected ?Model $model = null)
    {
        $this->paddingCharacter = config('sequences.padding_character');
    }

    /**
     * Get the unique key for the sequence.
     */
    public function getKey(): string
    {
        return $this->key ?? class_basename(static::class);
    }

    /**
     * Get the pattern for formatting the sequence.
     */
    public function getPattern(): string
    {
        return $this->pattern ?? config('sequences.default_pattern');
    }

    public function getResetPolicy(): ResetPolicy
    {
        if ($this->resetPolicy) {
            return $this->resetPolicy;
        }

        $policy = strtolower(config('sequences.default_reset_policy'));

        $policies = ['none', 'yearly', 'monthly', 'daily'];

        if (!in_array($policy, $policies)) {
            $message = "Reset policy '{$policy}' not supported";
            throw new InvalidArgumentException($message);
        }

        return match ($policy) {
            'yearly' => ResetPolicy::Yearly,
            'monthly' => ResetPolicy::Monthly,
            'daily' => ResetPolicy::Daily,
            default => ResetPolicy::None
        };
    }

    /**
     * Get the current reset policy value (year, month, day, or null).
     */
    public function getResetPolicyValue(): ?string
    {
        $policy = $this->getResetPolicy();
        $now = Carbon::now();

        return match ($policy) {
            ResetPolicy::Yearly => $now->format('Y'),
            ResetPolicy::Monthly => $now->format('Y-m'),
            ResetPolicy::Daily => $now->format('Y-m-d'),
            default => null
        };
    }

    /**
     * Generate and return the formatted sequence string.
     */
    public function getFormattedSequence(): string
    {
        $model = $this->model;
        $resetValue = $this->getResetPolicyValue();
        $attributes = [
            'key' => $this->getKey(),
            'reset_value' => $resetValue,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model?->getKey(),
        ];

        $sequence = Sequence::query()
            ->where($attributes)
            ->lockForUpdate()
            ->first();

        if (! $sequence) {
            $attributes['last_value'] = 0;
            $sequence = new Sequence($attributes);
            $sequence->save();
        }

        $sequence->increment('last_value');

        return $this->format($sequence->last_value);
    }

    /**
     * Generate a new formatted sequence.
     */
    public static function generate(?Model $model = null): string
    {
        $instance = new static($model);

        return $instance->getFormattedSequence();
    }

    /**
     * Format the sequence counter based on the pattern.
     *
     * Supported placeholders:
     *  - {YYYY}, {YY}, {MM}, {DD}, {H}, {M}, {S}
     *  - {SEQ} or {SEQ:N} for zero-padded sequence numbers
     */
    public function format(int $counter): string
    {
        $pattern = $this->getPattern();

        $now = Carbon::now();

        $replacements = [
            '{YYYY}' => $now->format('Y'),
            '{YY}' => $now->format('y'),
            '{MM}' => $now->format('m'),
            '{DD}' => $now->format('d'),
            '{H}' => $now->format('H'),
            '{M}' => $now->format('i'),
            '{S}' => $now->format('s'),
        ];

        $formatted = str_replace(array_keys($replacements), array_values($replacements), $pattern);

        if (preg_match('/\{SEQ:(\d+)\}/', $formatted, $matches)) {
            $padding = (int) $matches[1];
            $paddedSequence = str_pad($counter, $padding, $this->paddingCharacter, STR_PAD_LEFT);
            $formatted = str_replace($matches[0], $paddedSequence, $formatted);
        } elseif (str_contains($formatted, '{SEQ}')) {
            $formatted = str_replace('{SEQ}', (string) $counter, $formatted);
        }

        return $formatted;
    }
}
