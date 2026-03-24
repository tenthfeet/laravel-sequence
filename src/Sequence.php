<?php

namespace Tenthfeet\Sequence;

use Illuminate\Support\Facades\DB;
use Tenthfeet\Sequence\Models\Sequence as SequenceModel;

final class Sequence
{
    private function __construct(
        private SequenceDefinition $definition
    ) {}

    public static function using(SequenceDefinition $definition): self
    {
        return new self($definition);
    }

    public function rollback(int $steps = 1): void
    {
        if ($steps < 0) {
            throw new \InvalidArgumentException('Steps must be a non-negative integer.');
        }

        DB::transaction(function () use ($steps) {
            $row = SequenceModel::query()
                ->where($this->attributes())
                ->lockForUpdate()
                ->first();

            if (!$row) {
                return;
            }

            // Prevent unsigned underflow
            $newValue = max(0, $row->last_value - $steps);

            $row->update([
                'last_value' => $newValue,
            ]);
        });
    }

    public function next(): string
    {
        return DB::transaction(function () {
            $row = $this->lockRow();
            $row->increment('last_value');

            return (new SequenceFormatter($this->definition))
                ->format($row->last_value);
        });
    }

    public function previewNext(): string
    {
        $row = $this->getRow();
        $next = ($row?->last_value ?? 0) + 1;

        return (new SequenceFormatter($this->definition))
            ->format($next);
    }

    private function getRow(): ?SequenceModel
    {
        return SequenceModel::query()
            ->where($this->attributes())
            ->first();
    }

    private function lockRow(): SequenceModel
    {
        $row = SequenceModel::query()
            ->where($this->attributes())
            ->lockForUpdate()
            ->first();

        if (! $row) {
            $row = SequenceModel::create(
                array_merge($this->attributes(), ['last_value' => 0])
            );
        }

        return $row;
    }

    private function attributes(): array
    {
        $model = $this->definition->getModel();

        return [
            'key' => $this->definition->getKey(),
            'reset_value' => $this->definition->getResetPolicyValue(),
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model?->getKey(),
        ];
    }
}
