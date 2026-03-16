<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Tenthfeet\Sequence\Sequence;
use Tenthfeet\Sequence\Tests\Fixtures\InvoiceSequence;

it('generates first sequence correctly', function () {
    $seq = Sequence::using(
        new InvoiceSequence(Carbon::create(2024, 4, 1))
    );

    expect($seq->next())->toBe('2024-25/0001');
});

it('increments sequence value', function () {
    $seq = Sequence::using(
        new InvoiceSequence(Carbon::create(2024, 4, 1))
    );

    $first = $seq->next();
    $second = $seq->next();

    expect($first)->toBe('2024-25/0001')
        ->and($second)->toBe('2024-25/0002');
});

it('resets sequence on financial year change', function () {
    $firstFY = Sequence::using(
        new InvoiceSequence(Carbon::create(2024, 3, 31))
    );

    $secondFY = Sequence::using(
        new InvoiceSequence(Carbon::create(2024, 4, 1))
    );

    expect($firstFY->next())->toBe('2023-24/0001')
        ->and($secondFY->next())->toBe('2024-25/0001');
});

it('does not increment counter on preview', function () {
    $seq = Sequence::using(
        new InvoiceSequence(Carbon::create(2024, 4, 1))
    );

    $preview = $seq->previewNext();
    $actual = $seq->next();

    expect($preview)->toBe('2024-25/0001')
        ->and($actual)->toBe('2024-25/0001');
});

it('rolls back sequence increment with transaction', function () {
    try {
        DB::transaction(function () {
            $seq = Sequence::using(
                new InvoiceSequence(Carbon::create(2024, 4, 1))
            );

            $seq->next();

            throw new Exception('rollback');
        });
    } catch (Exception) {
        // swallow
    }

    $seq = Sequence::using(
        new InvoiceSequence(Carbon::create(2024, 4, 1))
    );

    expect($seq->next())->toBe('2024-25/0001');
});
