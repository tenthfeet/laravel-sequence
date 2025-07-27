<?php

return [

    /**
     * The database table where sequences will be stored
     */
    'table_name' => 'sequences',

    /**
     * The default sequence pattern used if not explicitly provided
     *
     * Example: '{SEQ:3}','INV-{YYYY}-{SEQ:2}'
     */
    'default_pattern' => '{SEQ:3}',

    /**
     * Defines how often the sequence should reset:
     *
     * Options: 'none' (never resets), 'yearly', 'monthly', 'daily'
     */
    'default_reset_policy' => 'none',

    /**
     * Character used to pad the sequence number (e.g., '0' -> 001, 002)
     */
    'padding_character' => '0',

];
