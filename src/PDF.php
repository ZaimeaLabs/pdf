<?php

declare(strict_types=1);

namespace Zaimea\PDF;

use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Zaimea\PDF\Invoice;
use Zaimea\PDF\Report;

class PDF
{
    /**
     * Generate the PDF.
     *
     * @param  \Zaimea\PDF\Invoice|\Zaimea\PDF\Report  $create
     * @param  string  $template
     * @return \Dompdf\Dompdf
     */
    public static function generate(Invoice|Report $create, string $template): Dompdf
    {
        $customHtmlTemplate = false;
        $tempFilepath = null;

        // Configurable flags (safe defaults)
        $isRemoteEnabled   = (bool) config('pdf.is_remote_enabled', true);
        $isPhpEnabled      = (bool) config('pdf.is_php_enabled', false); // SAFE default: false
        $allowInsecureSsl  = (bool) config('pdf.allow_insecure_ssl', false);
        $tempDir           = config('pdf.temp_dir', storage_path('framework/cache/pdf'));

        // Ensure temp dir exists with safe permissions (respect umask)
        if (! File::exists($tempDir)) {
            File::makeDirectory($tempDir, 0755, true);
        }

        // If $template contains raw HTML we render it via a temporary blade file
        if (self::containsHtml($template)) {
            $customHtmlTemplate = true;

            $tmp = tempnam($tempDir, 'zaimea_pdf_');
            if ($tmp === false) {
                throw new \RuntimeException('Unable to create temporary file for PDF template.');
            }

            // Ensure a .blade.php extension for clarity (View::file doesn't require it but fine)
            $tempFilepath = $tmp . '.blade.php';
            rename($tmp, $tempFilepath);

            file_put_contents($tempFilepath, trim($template));
        }

        $options = new Options();
        $options->set('isRemoteEnabled', $isRemoteEnabled);
        $options->set('isPhpEnabled', $isPhpEnabled);
        $options->setTempDir($tempDir);
        $options->set('fontCache', $tempDir);

        $pdf = new Dompdf($options);

        // Stream context: only relax SSL if explicitly allowed in config (don't blind-disable)
        $context = stream_context_create([
            'ssl' => [
                'verify_peer'      => $allowInsecureSsl ? false : true,
                'verify_peer_name' => $allowInsecureSsl ? false : true,
                'allow_self_signed'=> $allowInsecureSsl ? true  : false,
            ],
        ]);
        $pdf->setHttpContext($context);

        // Share view data (avoid globals)
        View::share('with_pagination', $create->with_pagination ?? false);

        try {
            if (self::containsHtml($template)) {
                $html = Blade::render($template, ['data' => $create]);
            } else {
                $html = View::make($template, ['data' => $create])->render();
            }

            $pdf->loadHtml($html);
            $pdf->render();

            return $pdf;
        } finally {
            // Always remove temp file if any
            if ($customHtmlTemplate && $tempFilepath !== null && File::exists($tempFilepath)) {
                @unlink($tempFilepath);
            }
        }
    }

    /**
     * Checks if the given string looks like a html.
     *
     * @param  string  $string
     * @return bool
     */
    protected static function containsHtml(string $string): bool
    {
        return preg_match("/<[^<]+>/", $string, $m) !== 0;
    }
}
