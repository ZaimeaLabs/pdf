<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Support\Facades\File;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Zaimea\PDF\PDFServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            PDFServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        // ensure our package config keys exist and point to a test temp dir
        $testTempDir = storage_path('framework/cache/pdf_tests');

        $app['config']->set('pdf.temp_dir', $testTempDir);
        $app['config']->set('pdf.is_remote_enabled', false);
        $app['config']->set('pdf.is_php_enabled', false);
        $app['config']->set('pdf.allow_insecure_ssl', true); // allow in CI/test if needed

        // make sure the temp dir is clean before each test run
        if (File::exists($testTempDir)) {
            File::deleteDirectory($testTempDir);
        }

        // create a clean temp dir with safe permissions
        File::makeDirectory($testTempDir, 0755, true);
    }

    protected function tearDown(): void
    {
        // cleanup temp dir after tests
        $testTempDir = storage_path('framework/cache/pdf_tests');
        if (File::exists($testTempDir)) {
            File::deleteDirectory($testTempDir);
        }

        parent::tearDown();
    }
}
