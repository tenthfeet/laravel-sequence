<?php

use Carbon\Month;
use Tenthfeet\Sequence\Enums\ResetPolicy;

return [

    /*
    |--------------------------------------------------------------------------
    | Sequences Table
    |--------------------------------------------------------------------------
    |
    | The database table used to store sequence counters.
    |
    */
    'table' => 'sequences',

    /*
    |--------------------------------------------------------------------------
    | Default Pattern
    |--------------------------------------------------------------------------
    |
    | Used when a sequence definition does not explicitly define a pattern.
    |
    */
    'default_pattern' => '{SEQ:3}',

    /*
    |--------------------------------------------------------------------------
    | Default Padding Character
    |--------------------------------------------------------------------------
    |
    | Used for zero-padding the sequence number.
    |
    */
    'default_padding_character' => '0',

    /*
    |--------------------------------------------------------------------------
    | Default Reset Policy
    |--------------------------------------------------------------------------
    |
    | none | yearly | monthly | daily | financial_year
    |
    */
    'default_reset_policy' => ResetPolicy::None,

    /*
    |--------------------------------------------------------------------------
    | Financial Year Defaults
    |--------------------------------------------------------------------------
    */
    'financial_year' => [

        /*
         | Financial year starting month
         */
        'start_month' => Month::April,
    ],
];
