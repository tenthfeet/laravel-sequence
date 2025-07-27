
# Laravel Sequence 

A flexible Laravel package for generating sequential numbers with various patterns and reset policies. Perfect for generating invoice numbers, order IDs, task numbers, and more!


## ✨ Features

- Global Sequences: Generate unique serials across your application (e.g., SR001, SR002)
- Model-Specific Sequences: Generate unique sequences per model record (e.g., Project 1: TASK-01,TASK-02, Project 2: TASK-01).
- Pattern-Based Generation: Define custom formats using placeholders like {YYYY}, {MM}, {DD}, {SEQ:N}.
- Reset Policies: Automatically reset sequences yearly, monthly, daily, or never.


## 🚀 Installation

Require the package via Composer:

```bash
  composer require tenthfeet/laravel-sequences
```

Publish the configuration file:

```bash
  php artisan vendor:publish --provider="Tenthfeet\Sequence\SequenceServiceProvider"
```
This will create `config` file where you can customize default settings and `migration` file.

Run the migrations:

```bash
  php artisan migrate
```
This will create the `sequences` table in your database, which is used to store the current state of your sequences.

## ⚙️ Configuration (config/sequences.php)

```php
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
```
## 📖 Usage and Examples

Create the `Sequence` class with the below artisan command.

```bash
    php artisan make:sequence InvoiceSequence
```

Now the generated Sequence class will like below

```php
    namespace App\Sequences;

    class InvoiceSequence extends SequenceGenerator{

        protected ?string $key='InvoiceSequence';

        protected ?string $pattern = '{SEQ:3}';

        protected ?ResetPolicy $resetPolicy = ResetPolicy::None;
    }
```
Global Sequences
```php
    use App\Sequences\InvoiceSequence;

    // default pattern {SEQ:3}
    InvoiceSequence::generate(); // 001
    InvoiceSequence::generate(); // 002
    InvoiceSequence::generate(); // 003

    // new pattern "INV-{YYYY}-{SEQ:3}" 
    InvoiceSequence::generate(); // INV-2025-001
    InvoiceSequence::generate(); // INV-2025-002
    InvoiceSequence::generate(); // INV-2025-003

```

Model-Specific Sequences
```php
    use App\Sequences\ProjectTaskSequence;

    // default pattern TASK-{SEQ:2}
    $modelOne=Project::find(1);
    ProjectTaskSequence::generate($modelOne); // TASK-01
    ProjectTaskSequence::generate($modelOne); // TASK-02

    $modelTwo=Project::find(2);
    ProjectTaskSequence::generate($modelTwo); // TASK-01
    ProjectTaskSequence::generate($modelTwo); // TASK-02

    /**
     * If you need more dynamic pattern like prefix value model
     * just override like below
     */
    class ProjectTaskSequence extends SequenceGenerator{

        protected ?string $key='ProjectTaskSequence';

        protected ?ResetPolicy $resetPolicy = ResetPolicy::None;

        public function getPattern(){
            // model passed from generate can be accessed like $this->model

            $prefix = $this->model->prefix;

            return "{$prefix}/TASK/{SEQ}";
        }
    }

    // Examples
    $modelOne=Project::find(1); // prefix = ABC
    ProjectTaskSequence::generate($modelOne); // ABC/TASK/1
    ProjectTaskSequence::generate($modelOne); // ABC/TASK/2

    $modelTwo=Project::find(2); // prefix = XYZ
    ProjectTaskSequence::generate($modelTwo); // XYZ/TASK/1
    ProjectTaskSequence::generate($modelTwo); // XYZ/TASK/2
    
```

Pattern Placeholders

| Placeholder | Description | Example (Current Date: 2024-07-21 09:30:15) |
|:-------:|:------:|:------:|
| {YYYY} | Full year | 2024 |
| {YY} | Two-digit year | 24 |
| {MM} | Month (01-12) | 07 |
| {DD} | Day (01-31) | 21 |
| {H} | Hour (00-23) | 09 |
| {M} | Minute (00-59) | 30 |
| {S} | Second (00-59) | 15 |
| {SEQ} | Raw sequence number | 1, 10, 123 |
| {SEQ:N} | Padded sequence number | {SEQ:4} for 1 becomes 0001 |

Reset Policies

You can define how often a sequence should reset with `$resetPolicy` in the Sequence class.

```php
use Tenthfeet\Sequences\ResetPolicy;

// There are four reset policy available.
ResetPolicy::None; // Counter will not reset.
ResetPolicy::Yearly; // counter reset Jan-01 of every year.
ResetPolicy::Monthly; // counter reset 1st day of every month.
ResetPolicy::Daily; // counter resets everyday.
```
