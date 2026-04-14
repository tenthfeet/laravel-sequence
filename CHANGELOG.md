# Changelog

## [2.3.0] - 2026-04-14

- Added support for Laravel 13.
- Added support for PHP 8.3, 8.4, and 8.5.
- Updated `orchestra/testbench` for Laravel 13 compatibility.
- Updated compatibility documentation in README.

## [2.2.0] - 2026-03-24

- Updated docs to match current API (`Sequence::using(...)->next()`, `SequenceDefinition` fluent setters).
- Added pattern tokens and financial year usage details.
- Added model-scoped sequence examples and configuration notes.

## [1.0.0] - 2026-03-16

- Initial package implementation: sequence definitions, DB-backed counter, pattern formatting, reset policies.
- Added `make:sequence` command and config publish.
