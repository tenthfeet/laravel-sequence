<?php

namespace Tenthfeet\Sequence\Models;

use Illuminate\Database\Eloquent\Model;

class Sequence extends Model
{
    protected $guarded = [];

    public function getTable(): string
    {
        return config('sequences.table_name', 'sequences');
    }

    public function casts(): array
    {
        return [
            'last_reset_at' => 'datetime',
            'last_value' => 'integer',
        ];
    }
}
