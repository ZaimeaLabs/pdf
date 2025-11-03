<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Remote Assets
    |--------------------------------------------------------------------------
    |
    | This option controls whether Dompdf is allowed to load remote assets such
    | as images, fonts, or stylesheets over HTTP/HTTPS. Disable this if you want
    | to restrict PDF rendering to local resources only for security or
    | performance reasons. You may override this per generated PDF.
    |
    */
    'is_remote_enabled' => env('PDF_REMOTE_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | PHP Execution in Templates
    |--------------------------------------------------------------------------
    |
    | When enabled, Dompdf will allow execution of PHP code inside templates,
    | either through <?php ?> tags or <script type="text/php"> blocks. For
    | security reasons, this is disabled by default. Only enable this if your
    | templates are fully trusted and controlled by your application.
    |
    */
    'is_php_enabled' => env('PDF_PHP_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Insecure SSL
    |--------------------------------------------------------------------------
    |
    | This setting controls whether Dompdf should bypass SSL certificate
    | verification when fetching remote assets. It is disabled by default and
    | should only be used in development environments or when working with
    | self-signed certificates. Never enable this in production.
    |
    */
    'allow_insecure_ssl' => env('PDF_ALLOW_INSECURE_SSL', false),

    /*
    |--------------------------------------------------------------------------
    | Temporary Directory
    |--------------------------------------------------------------------------
    |
    | The directory Dompdf will use for temporary files, including its font
    | cache, downloaded images, and internal processing. By default, it points
    | to storage/framework/cache/pdf. Make sure this directory is writable by
    | your application. You may customize the path to match your deployment
    | environment.
    |
    */
    'temp_dir' => env('PDF_TEMP_DIR', storage_path('framework/cache/pdf')),

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
    'logo' => null,

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
