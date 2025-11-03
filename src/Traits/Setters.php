<?php

declare(strict_types=1);

namespace Zaimea\PDF\Traits;

use Carbon\Carbon;
use Illuminate\Support\Collection;

trait Setters
{
    /**
     * Set the name.
     *
     * @param string $name
     * @return self
     */
    public function name($name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Set the number.
     *
     * @param int $number
     * @return self
     */
    public function number($number): self
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Set the decimal precision.
     *
     * @param int $decimals
     * @return self
     */
    public function decimals($decimals): self
    {
        $this->decimals = $decimals;

        return $this;
    }

    /**
     * Set the logo URL.
     *
     * @param string $logo_url
     * @return self
     */
    public function logo($logo_url): self
    {
        $this->logo = $logo_url;

        return $this;
    }

    /**
     * Set the date.
     *
     * @param \Carbon\Carbon $date
     * @return self
     */
    public function date(Carbon $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Set the notes.
     *
     * @param string $notes
     * @return self
     */
    public function notes($notes): self
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Set the business details.
     *
     * @param array $details
     * @return self
     */
    public function business($details): self
    {
        $this->business_details = Collection::make($details);

        return $this;
    }

    /**
     * Set the customer details.
     *
     * @param array $details
     * @return self
     */
    public function customer($details): self
    {
        $this->customer_details = Collection::make($details);

        return $this;
    }

    /**
     * Set the invoice currency.
     *
     * @param string $currency
     * @return self
     */
    public function currency($currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Set the footnote.
     *
     * @param string $footnote
     * @return self
     */
    public function footnote($footnote): self
    {
        $this->footnote = $footnote;

        return $this;
    }

    /**
     * Set the due date.
     *
     * @param \Carbon\Carbon $due_date
     * @return self
     */
    public function due_date(?Carbon $due_date = null): self
    {
        $this->due_date = $due_date;
        return $this;
    }

    /**
     * Show/hide the pagination.
     *
     * @param boolean $with_pagination
     * @return self
     */
    public function with_pagination($with_pagination): self
    {
        $this->with_pagination = $with_pagination;
        return $this;
    }

    /**
     * Duplicate the header on each page.
     *
     * @param boolean $duplicate_header
     * @return self
     */
    public function duplicate_header($duplicate_header): self
    {
        $this->duplicate_header = $duplicate_header;
        return $this;
    }
}
