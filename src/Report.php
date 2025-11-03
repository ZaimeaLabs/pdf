<?php

declare(strict_types=1);

namespace Zaimea\PDF;

use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Zaimea\PDF\Traits\Setters;

class Report
{
    use Setters;

    /**
     * Report name.
     *
     * @var string
     */
    public $name;

    /**
     * Report template.
     *
     * @var string
     */
    public $template;

    /**
     * Report item collection.
     *
     * @var \Illuminate\Support\Collection
     */
    public $items;

    /**
     * Report check collection.
     *
     * @var \Illuminate\Support\Collection
     */
    public $checks;

    /**
     * Report number.
     *
     * @var int
     */
    public $number = null;

    /**
     * Report decimal precision.
     *
     * @var int
     */
    public $decimals;

    /**
     * Report logo.
     *
     * @var string
     */
    public $logo;

    /**
     * Report Logo Height.
     *
     * @var int
     */
    public $logo_height;

    /**
     * Report Date.
     *
     * @var \Carbon\Carbon
     */
    public $date;

    /**
     * Report Notes.
     *
     * @var string
     */
    public $notes;

    /**
     * Report Business Details.
     *
     * @var array
     */
    public $business_details;

    /**
     * Report Customer Details.
     *
     * @var array
     */
    public $customer_details;

    /**
     * Report Footnote.
     *
     * @var array
     */
    public $footnote;

    /**
     * Report pagination.
     *
     * @var boolean
     */
    public $with_pagination;

    /**
     * Report header duplication.
     *
     * @var boolean
     */
    public $duplicate_header;

    /**
     * Stores the PDF object.
     *
     * @var \Dompdf\Dompdf
     */
    private $pdf;

    /**
     * Create a new report instance.
     *
     * @param string $name
     */
    public function __construct($name = 'Report')
    {
        $this->name             = $name;
        $this->template         = 'pdf::report';
        $this->items            = Collection::make([]);
        $this->checks           = Collection::make([]);
        $this->decimals         = config('pdf.decimals');
        $this->logo             = config('pdf.logo');
        $this->logo_height      = config('pdf.logo_height');
        $this->date             = Carbon::now();
        $this->business_details = Collection::make(config('pdf.business_details'));
        $this->customer_details = Collection::make([]);
        $this->footnote         = config('pdf.footnote');
        $this->with_pagination  = config('pdf.with_pagination');
        $this->duplicate_header = config('pdf.duplicate_header');
    }

    /**
     * Return a new instance of Report.
     *
     * @param string $name
     * @return \Zaimea\PDF\Report
     */
    public static function make($name = 'Report'): Report
    {
        return new self($name);
    }

    /**
     * Select template for report.
     *
     * @param string $template
     * @return self
     */
    public function template($template = 'pdf::report'): self
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Adds an item to the report.
     *
     * @param string $date
     * @param string $name
     * @param string $start
     * @param string $end
     * @param string $pause
     * @param string $duration
     * @param string $approved
     * @param string $notes
     * @param array  $extra
     * @return self
     */
    public function addItem($date, $name, $start = null, $end = null, $pause = null, $duration = null, $notes = null, $extra = []): self
    {
        $this->items->push(Collection::make([
            'date'     => $date,
            'name'     => $name,
            'start'    => $start,
            'end'      => $end,
            'pause'    => $pause,
            'duration' => $duration,
            'notes'    => $notes,
            'extra'    => $extra,
        ]));

        return $this;
    }

    /**
     * Adds checks to the report for template.
     * @param string $start
     * @param string $end
     * @param string $pause
     * @param string $duration
     * @param string $notes
     * @param array  $extra
     *
     * @return self
     */
    public function addChecks($start, $end, $pause, $duration, $notes, $extra = []): self
    {
        $this->checks = Collection::make([
            'start'    => $start,
            'end'      => $end,
            'pause'    => $pause,
            'duration' => $duration,
            'notes'    => $notes,
            'extra'    => $extra,
        ]);

        return $this;
    }

    /**
     * Pop the last report item.
     *
     * @return self
     */
    public function popItem(): self
    {
        $this->items->pop();

        return $this;
    }

    /**
     * Generate the PDF.
     *
     * @return self
     */
    private function generate(): self
    {
        $this->pdf = PDF::generate($this, $this->template);

        return $this;
    }

    /**
     * Output the PDF as a string.
     *
     * @return string The rendered PDF as string
     */
    public function output(): string
    {
        $this->generate();

        return (string) $this->pdf->output();
    }

    /**
     * Downloads the generated PDF.
     *
     * @param string $name
     * @return \Illuminate\Http\Response
     */
    public function download($name = 'report.pdf'): Response
    {
        $this->generate();

        return new Response($this->pdf->output(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $name . '"',
            'Content-Length'      => strlen($this->pdf->output()),
        ]);
    }

    /**
     * Save the generated PDF.
     *
     * @param string $name
     */
    public function save($name = 'report.pdf')
    {
        $report = $this->generate();

        Storage::put($name, $report->pdf->output());
    }

    /**
     * Show the PDF in the browser.
     *
     * @param string $name
     * @return \Illuminate\Http\Response
     */
    public function show($name = 'report.pdf'): Response
    {
        $this->generate();

        return new Response($this->pdf->output(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' =>  'inline; filename="' . $name . '"',
        ]);
    }
}
