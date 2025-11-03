<?php

declare(strict_types=1);

namespace Zaimea\PDF;

use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Zaimea\PDF\Traits\Setters;

class Invoice
{
    use Setters;

    /**
     * Invoice name.
     *
     * @var string
     */
    public $name;

    /**
     * Invoice template.
     *
     * @var string
     */
    public $template;

    /**
     * Invoice item collection.
     *
     * @var \Illuminate\Support\Collection
     */
    public $items;

    /**
     * Invoice currency.
     *
     * @var string
     */
    public $currency;

    /**
     * Invoice number.
     *
     * @var int
     */
    public $number = null;

    /**
     * Invoice decimal precision.
     *
     * @var int
     */
    public $decimals;

    /**
     * Invoice logo.
     *
     * @var string
     */
    public $logo;

    /**
     * Invoice Logo Height.
     *
     * @var int
     */
    public $logo_height;

    /**
     * Invoice Date.
     *
     * @var \Carbon\Carbon
     */
    public $date;

    /**
     * Invoice Notes.
     *
     * @var string
     */
    public $notes;

    /**
     * Invoice Business Details.
     *
     * @var array
     */
    public $business_details;

    /**
     * Invoice Customer Details.
     *
     * @var array
     */
    public $customer_details;

    /**
     * Invoice Footnote.
     *
     * @var array
     */
    public $footnote;

    /**
     * Invoice Tax Rates Default.
     *
     * @var array
     */
    public $tax_rates;

    /**
     * Invoice Due Date.
     *
     * @var \Carbon\Carbon
     */
    public $due_date = null;

    /**
     * Invoice pagination.
     *
     * @var boolean
     */
    public $with_pagination;

    /**
     * Invoice header duplication.
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
     * Json of currencies
     *
     * @var object
     */
    private $currencies;

    /**
     * Create a new invoice instance.
     *
     * @param string $name
     */
    public function __construct($name = 'Invoice')
    {
        $this->name             = $name;
        $this->template         = 'pdf::invoice';
        $this->items            = Collection::make([]);
        $this->currency         = config('pdf.currency');
        $this->decimals         = config('pdf.decimals');
        $this->logo             = config('pdf.logo');
        $this->logo_height      = config('pdf.logo_height');
        $this->date             = Carbon::now();
        $this->business_details = Collection::make(config('pdf.business_details'));
        $this->customer_details = Collection::make([]);
        $this->footnote         = config('pdf.footnote');
        $this->tax_rates        = config('pdf.tax_rates');
        $this->due_date         = config('pdf.due_date') != null ? Carbon::parse(config('pdf.due_date')) : null;
        $this->with_pagination  = config('pdf.with_pagination');
        $this->duplicate_header = config('pdf.duplicate_header');
    }

    /**
     * Return a new instance of Invoice.
     *
     * @param string $name
     * @return \Zaimea\PDF\Invoice
     */
    public static function make($name = 'Invoice'): Invoice
    {
        return new self($name);
    }

    /**
     * Select template for invoice.
     *
     * @param string $template
     * @return self
     */
    public function template($template = 'pdf::invoice'): self
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Adds an item to the invoice.
     *
     * @param string $name
     * @param string $price
     * @param string $amount
     * @param string $id
     * @param string $imageUrl
     * @return self
     */
    public function addItem($name, $price, $amount = 1, $id = '-', $imageUrl = null): self
    {
        $this->items->push(Collection::make([
            'name'       => $name,
            'price'      => $price,
            'amount'     => $amount,
            'totalPrice' => number_format((float) bcmul($price, $amount, $this->decimals), $this->decimals, $this->getDecPoint(), $this->getThousandsSep()),
            'id'         => $id,
            'imageUrl'   => $imageUrl,
        ]));

        return $this;
    }

    /**
     * Pop the last invoice item.
     *
     * @return self
     */
    public function popItem(): self
    {
        $this->items->pop();

        return $this;
    }

    /**
     * Return the currency object.
     *
     * @return \stdClass
     */
    public function formatCurrency(): \stdClass
    {
        if(null === $this->currencies) {
            $this->currencies = json_decode(file_get_contents(__DIR__ . '/../Currencies.json'));
        }
        $currency = $this->currency;

        return $this->currencies->$currency;
    }

    /**
     * Return the subtotal invoice price.
     *
     * @return int
     */
    public function subTotalPrice(): int
    {
        return $this->items->sum(function ($item) {
            return (int)bcmul($item['price'], $item['amount'], $this->decimals);
        });
    }

    /**
     * Return formatted sub total price.
     *
     * @return string
     */
    public function subTotalPriceFormatted(): string
    {
        return number_format($this->subTotalPrice(), $this->decimals, $this->getDecPoint(), $this->getThousandsSep());
    }

    /**
     * Return the total invoce price after aplying the tax.
     *
     * @return float
     */
    public function totalPrice(): float
    {
        return floatval(bcadd(strval($this->subTotalPrice()), (string)$this->taxPrice(), $this->decimals));
    }

    /**
     * Return formatted total price.
     *
     * @return string
     */
    public function totalPriceFormatted(): string
    {
        return number_format($this->totalPrice(), $this->decimals, $this->getDecPoint(), $this->getThousandsSep());
    }

    /**
     * taxPrice.
     *
     * @param  object $tax_rate
     * @return mixed
     */
    public function taxPrice(?Object $tax_rate = null): mixed
    {
        if (is_null($tax_rate)) {
            $tax_total = 0;
            foreach($this->tax_rates as $taxe){
                if ($taxe['tax_type'] == 'percentage') {
                    $tax_total += bcdiv(bcmul((string)$taxe['tax'], strval($this->subTotalPrice()), $this->decimals), '100', $this->decimals);
                    continue;
                }
                $tax_total += $taxe['tax'];
            }
            return $tax_total;
        }

        if ($tax_rate->tax_type == 'percentage') {
            return bcdiv(bcmul((string)$tax_rate->tax, strval($this->subTotalPrice()), $this->decimals), '100', $this->decimals);
        }

        return $tax_rate->tax;
    }

    /**
     * Return formatted tax.
     *
     * @param  ?object  $tax_rate;
     * @return string
     */
    public function taxPriceFormatted($tax_rate): string
    {
        return number_format((int)$this->taxPrice($tax_rate), $this->decimals, $this->getDecPoint(), $this->getThousandsSep());
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
     * Downloads the generated PDF.
     *
     * @param string $name
     * @return \Illuminate\Http\Response
     */
    public function download($name = 'invoice.pdf'): Response
    {
        $this->generate();

        return new Response($this->pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' =>  'attachment; filename="' . $name . '"',
            'Content-Length' => strlen($this->pdf->output()),
        ]);
    }

    /**
     * Save the generated PDF.
     *
     * @param string $name
     *
     */
    public function save($name = 'invoice.pdf')
    {
        $invoice = $this->generate();

        Storage::put($name, $invoice->pdf->output());
    }

    /**
     * Show the PDF in the browser.
     *
     * @param string $name
     * @return \Illuminate\Http\Response
     */
    public function show($name = 'invoice.pdf'): Response
    {
        $this->generate();

        return new Response($this->pdf->output(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' =>  'inline; filename="' . $name . '"',
        ]);
    }

    /**
     * Return true/false if one item contains image.
     * Determine if we should display or not the image column on the invoice.
     *
     * @return boolean
     */
    public function shouldDisplayImageColumn(): bool
    {
        foreach($this->items as $item){
            if($item['imageUrl'] != null){
                return true;
            }
        }
        return false;
    }

    /**
     * return the decimal point for number format
     *
     * @method getDecPoint
     *
     * @return string
     */
    protected function getDecPoint()
    {
        $format = $this->formatCurrency();
        return $this->formatCurrency()->dec_point ?: ".";
    }

    /**
     * return the thousands_sep for number format
     *
     * @method getThousandsSep
     *
     * @return string
     */
    protected function getThousandsSep()
    {
        return $this->formatCurrency()->thousands_sep ?: ",";
    }
}
