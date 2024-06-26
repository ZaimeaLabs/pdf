<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Currency
    |--------------------------------------------------------------------------
    |
    | This value is the default currency that is going to be used in invoices.
    | You can change it on each invoice individually.
    */
    'currency' => 'EUR',

    /*
    |--------------------------------------------------------------------------
    | Default Decimal Precision
    |--------------------------------------------------------------------------
    |
    | This value is the default decimal precision that is going to be used
    | to perform all the calculations.
    */
    'decimals' => 2,

    /*
    |--------------------------------------------------------------------------
    | Default Tax Rates
    |--------------------------------------------------------------------------
    |
    | This array group multiple tax rates.
    |
    | The tax type accepted values are: 'percentage' and 'fixed'.
    | The percentage type calculates the tax depending on the invoice price, and
    | the fixed type simply adds a fixed amount to the total price.
    | You can't mix percentage and fixed tax rates.
    */
    'tax_rates' => [
        [
            'name'      => '',
            'tax'       => 0,
            'tax_type'  => 'percentage',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default PDF Logo
    |--------------------------------------------------------------------------
    |
    | This value is the default pdf logo that is going to be used in pdfs.
    | You can change it on each pdf individually.
    */
    'logo' => 'https://cdn.custura.de/art/pdf-generator.jpg',

    /*
    |--------------------------------------------------------------------------
    | Default PDF Logo Height
    |--------------------------------------------------------------------------
    |
    | This value is the default pdf logo height that is going to be used in pdfs.
    | You can change it on each pdf individually.
    */
    'logo_height' => 60,

    /*
    |--------------------------------------------------------------------------
    | Default PDF Buissness Details
    |--------------------------------------------------------------------------
    |
    | This value is going to be the default attribute displayed in
    | the customer model.
    */
    'business_details' => [
        'name'        => env('APP_NAME', 'ZaimeaLabs'),
        'id'          => '1',
        'phone'       => '+49 123 456 789',
        'location'    => 'Muster Str. 1',
        'zip'         => '10115',
        'city'        => 'Muster Stadt',
        'country'     => 'Muster Land',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default PDF Footnote
    |--------------------------------------------------------------------------
    |
    | This value is going to be at the end of the document, sometimes telling you
    | some copyright message or simple legal terms.
    */
    'footnote' => '',

    /*
    | Default Invoice Due Date
    |--------------------------------------------------------------------------
    |
    | This value is the default due date that is going to be used in invoices.
    | You can change it on each invoice individually.
    | You can set it null to remove the due date on all invoices.
    */
    'due_date' => date('M dS ,Y',strtotime('+3 months')),

    /*
    | Default pagination parameter
    |--------------------------------------------------------------------------
    |
    | This value is the default pagination parameter.
    | If true and page count are higher than 1, pagination will show at the bottom.
    */
    'with_pagination' => true,

    /*
    | Duplicate header parameter
    |--------------------------------------------------------------------------
    |
    | This value is the default header parameter.
    | If true header will be duplicated on each page.
    */
    'duplicate_header' => false,
];
