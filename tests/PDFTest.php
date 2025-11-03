<?php

declare(strict_types=1);

use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Zaimea\PDF\Invoice;
use Zaimea\PDF\PDF;
use Zaimea\PDF\Report;

beforeEach(function () {
    $tempDir = storage_path('framework/cache/pdf');
    if (!file_exists($tempDir)) {
        mkdir($tempDir, 0777, true);
    }
    array_map('unlink', glob("$tempDir/*"));
});

it('generates a report and returns non-empty output', function () {
    $report = Report::make('Test Report')
        ->addItem(now()->format('Y-m-d'), 'John Doe', '08:00', '16:00', '00:30', '07:30', '')
        ->number(1)
        ->with_pagination(false)
        ->duplicate_header(false);

    $output = $report->output();

    expect($output)->toBeString()->and(strlen($output))->toBeGreaterThan(0);
});

it('invoice download returns a PDF response with correct headers', function () {
    $invoice = Invoice::make('Test Invoice')
        ->addItem('Item A', '10.00', '1')
        ->number(1001)
        ->with_pagination(false);

    /** @var Response $response */
    $response = $invoice->download('test-invoice.pdf');

    expect($response->getStatusCode())->toEqual(200);
    expect($response->headers->get('Content-Type'))->toContain('application/pdf');
    expect((int) $response->headers->get('Content-Length'))->toBeGreaterThan(0);
});

it('generating from raw html cleans up temp files', function () {
    // prepare a simple HTML template string (contains HTML tags to trigger temp file flow)
    $htmlTemplate = '<html><body><h1>Hello Test</h1><p>{{ $data->name ?? "No name" }}</p></body></html>';

    // create a simple Report instance
    $report = Report::make('TmpReport')
        ->addItem(now()->format('Y-m-d'), 'Tester', '08:00', '12:00', '00:30', '03:30', '')
        ->with_pagination(false);

    // call PDF::generate directly with HTML string
    $tempDir = config('pdf.temp_dir') ?: storage_path('framework/cache/pdf_tests');

    // ensure directory is empty before
    $filesBefore = File::files($tempDir);
    expect(count($filesBefore))->toBe(0);

    $pdf = PDF::generate($report, $htmlTemplate);

    // generation should return a Dompdf instance and produce output
    expect(method_exists($pdf, 'output'))->toBeTrue();
    $out = $pdf->output();
    expect(strlen($out))->toBeGreaterThan(0);

    // ensure no leftover temp files in temp dir (allowing for small timing margin)
    $filesAfter = File::files($tempDir);

    $realFiles = array_filter($filesAfter, function ($file) {
        return !str_ends_with($file->getFilename(), '.afm.json');
    });

    expect(count($realFiles))->toBe(0);
});

it('uses configured temp dir and creates it if missing', function () {
    $tempDir = storage_path('framework/cache/pdf_tests_custom');

    // ensure it's removed
    if (File::exists($tempDir)) {
        File::deleteDirectory($tempDir);
    }

    // temporarily set config to this new dir
    config()->set('pdf.temp_dir', $tempDir);

    // run a small generation (report)
    $report = Report::make('TempDirCheck')
        ->addItem(now()->format('Y-m-d'), 'Tester', '08:00', '16:00', '00:30', '07:30', '')
        ->with_pagination(false);

    $pdf = PDF::generate($report, 'pdf::report'); // use packaged view

    expect(File::exists($tempDir))->toBeTrue();
    expect(strlen($pdf->output()))->toBeGreaterThan(0);

    // cleanup
    if (File::exists($tempDir)) {
        File::deleteDirectory($tempDir);
    }
});
