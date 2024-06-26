<?php

declare(strict_types=1);

namespace ZaimeaLabs\PDF;

use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\View;

class PDF
{
    /**
     * Generate the PDF.
     *
     * @param \ZaimeaLabs\PDF\Invoice|\ZaimeaLabs\PDF\Report $create
     * @param string  $template
     * @return \Dompdf\Dompdf
     */
    public static function generate($create, $template): DomPdf
    {
        $template = strtolower($template);

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

        return $pdf;
    }
}
