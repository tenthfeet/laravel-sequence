# Laravel Sequence

A lightweight, flexible Laravel package for generating format-based sequential values with safe DB locking and reset policies.

## Features

- Concurrent-safe sequence generation with DB row locking
- Custom patterns: `{YYYY}`, `{MM}`, `{DD}`, `{H}`, `{M}`, `{S}`, `{SEQ}`, `{SEQ:N}`, `{FY}`
- Reset policies: `none`, `yearly`, `monthly`, `daily`, `financial_year`
- Model-scoped sequences (per record)
- Preview next value without incrementing

## Compatibility

- **PHP**: `8.2`, `8.3`, `8.4`, and `8.5`
- **Laravel**: `10.x`, `11.x`, `12.x`, and `13.x`

## Installation

Install via Composer:

```bash
composer require tenthfeet/laravel-sequence
```

Publish config and migration:

```bash
php artisan vendor:publish --provider="Tenthfeet\Sequence\SequenceServiceProvider" --tag="config"
php artisan vendor:publish --provider="Tenthfeet\Sequence\SequenceServiceProvider" --tag="migrations"
```

Run migrations:

```bash
php artisan migrate
```

## Configuration

Published config: `config/sequences.php`

```php
return [
    'table' => 'sequences',
    'default_pattern' => '{SEQ:3}',
    'default_padding_character' => '0',
    'default_reset_policy' => \Tenthfeet\Sequence\Enums\ResetPolicy::None,
    'financial_year' => [
        'start_month' => \Carbon\Month::April,
    ],
];
```

## Define a sequence

Create a sequence definition:

```bash
php artisan make:sequence InvoiceSequence
```

Example class:

```php
namespace App\Sequences;

use Tenthfeet\Sequence\SequenceDefinition;
use Tenthfeet\Sequence\Enums\ResetPolicy;

final class InvoiceSequence extends SequenceDefinition
{
    public function key(): string
    {
        return 'invoice';
    }

    public function __construct()
    {
        $this->pattern('INV-{YYYY}-{SEQ:4}')
            ->resetPolicy(ResetPolicy::Yearly);
    }
}
```

## Generate sequences

```php
use Tenthfeet\Sequence\Sequence;
use App\Sequences\InvoiceSequence;

$sequence = new InvoiceSequence();
$value = Sequence::using($sequence)->next();
$preview = Sequence::using($sequence)->previewNext();
```

### Model-specific sequences

```php
use App\Sequences\ProjectTaskSequence;
use Tenthfeet\Sequence\Sequence;

$project = App\Models\Project::find(1);
$definition = (new ProjectTaskSequence())->forModel($project);
$value = Sequence::using($definition)->next();
```

### Runtime overrides

```php
$definition = (new InvoiceSequence())
    ->pattern('INV-{YYYY}-{MM}-{SEQ:5}')
    ->padWith('0')
    ->resetPolicy(ResetPolicy::Monthly)
    ->financialYearStartsIn(\Carbon\Month::April)
    ->forModel($project)
    ->usingDate(now());
$value = Sequence::using($definition)->next();
```

#### Available fluent methods

- `pattern(string $pattern)` - Set the sequence pattern
- `padWith(string $char)` - Set the padding character for sequence numbers
- `resetPolicy(ResetPolicy $policy)` - Set when the sequence should reset
- `financialYearStartsIn(Month $month)` - Set the financial year start month
- `forModel(Model $model)` - Scope the sequence to a specific model instance
- `usingDate(Carbon $date)` - Override the date used for date-based tokens

## Pattern tokens

| Token | Output example | Description |
|---|---|---|
| `{YYYY}` | 2026 | 4-digit year |
| `{YY}` | 26 | 2-digit year |
| `{MM}` | 03 | month |
| `{DD}` | 16 | day |
| `{H}` | 13 | hour |
| `{M}` | 45 | minute |
| `{S}` | 09 | second |
| `{SEQ}` | 1 | counter raw |
| `{SEQ:N}` | 0001 | padded counter |
| `{FY}` | 2025-26 | financial year |
| `{FY:YY-YY}` | 25-26 | financial year |
| `{FY:YYYY-YY}` | 2025-26 | financial year |
| `{FY:YYYY-YYYY}` | 2025-2026 | financial year |

## Reset policies

```php
use Tenthfeet\Sequence\Enums\ResetPolicy;

ResetPolicy::None;
ResetPolicy::Yearly;
ResetPolicy::Monthly;
ResetPolicy::Daily;
ResetPolicy::FinancialYear;
```

### Rollback sequences

```php
use Tenthfeet\Sequence\Sequence;
use App\Sequences\InvoiceSequence;

$sequence = new InvoiceSequence();

// Rollback by 1 step (default)
Sequence::using($sequence)->rollback();

// Rollback by multiple steps
Sequence::using($sequence)->rollback(3);
```

## Notes

Sequence rows are grouped by `key`, `reset_value`, and optional `model_type` / `model_id`.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## License

MIT
