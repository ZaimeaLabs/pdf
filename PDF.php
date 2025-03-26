<?php

declare(strict_types=1);

namespace ZaimeaLabs\PDF;

use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\View;
use ZaimeaLabs\PDF\Invoice;
use ZaimeaLabs\PDF\Report;

class PDF
{
    /**
     * Generate the PDF.
     *
     * @param  \ZaimeaLabs\PDF\Invoice|\ZaimeaLabs\PDF\Report  $create
     * @param  string  $template
     * @return \Dompdf\Dompdf
     */
    public static function generate(Invoice|Report $create, string $template): DomPdf
    {
        $customHtmlTemplate = false;

        if(self::containsHtml($template)) {
            $customHtmlTemplate = true;

            $filename = uniqid('blade_',true);

            $path = storage_path("framework/views/tmp");

            View::addLocation($path);

            $filename = str_replace('.','',uniqid('blade_',true));

            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }

            file_put_contents($filepath, trim($template));

            $template = $filename;
        }

        $options = new Options();

        $options->set('isRemoteEnabled', true);
        $options->set('isPhpEnabled', true);

        $pdf = new Dompdf($options);

        $context = stream_context_create([
            'ssl' => [
                'verify_peer'      => false,
                'verify_peer_name' => false,
                'allow_self_signed'=> true,
            ],
        ]);

        $pdf->setHttpContext($context);

        $GLOBALS['with_pagination'] = $create->with_pagination;

        $pdf->loadHtml(View::make($template, ['data' => $create]));
        $pdf->render();

        if($customHtmlTemplate){
            unlink($filepath);
        }

        return $pdf;
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
