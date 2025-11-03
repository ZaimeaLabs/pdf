<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Storage;
use Zaimea\PDF\Invoice;
use Zaimea\PDF\Report;

it('saves invoice to storage using save()', function () {
    // fake the default storage disk
    Storage::fake();

    $invoice = Invoice::make('Save Invoice Test')
        ->addItem('Service A', '12.5', '1')
        ->number(555)
        ->with_pagination(false);

    // choose a path
    $path = 'pdfs/invoices/test-invoice-555.pdf';

    // call save() — the package uses Storage::put(...) internally
    $invoice->save($path);

    // assert file exists on the fake disk
    expect(Storage::exists($path))->toBeTrue();

    // assert content is non-empty and looks like a PDF (starts with %PDF)
    $content = Storage::get($path);
    expect(strlen($content))->toBeGreaterThan(0);
    expect(substr($content, 0, 4))->toBe('%PDF');
});

it('saves report to storage using save()', function () {
    Storage::fake();

    $report = Report::make('Save Report Test')
        ->addItem(now()->format('Y-m-d'), 'Jane Doe', '08:00', '16:00', '00:30', '07:30', '')
        ->number(42)
        ->with_pagination(false);

    $path = 'pdfs/reports/test-report-42.pdf';

    $report->save($path);

    expect(Storage::exists($path))->toBeTrue();

    $content = Storage::get($path);
    expect(strlen($content))->toBeGreaterThan(0);
    expect(substr($content, 0, 4))->toBe('%PDF');
});
