<?php

namespace Tenthfeet\Sequence\Models;

use Illuminate\Database\Eloquent\Model;

class Sequence extends Model
{
    protected $guarded = [];

    public function getTable(): string
    {
        return config('sequences.table', 'sequences');
    }

    public function casts(): array
    {
        return [
            'last_value' => 'integer',
        ];
    }
}
