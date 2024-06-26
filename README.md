<p align="center">
  <a href="https://zaimea.com/" target="_blank">
    <img src=".github/pdf.svg" alt="PDF" width="300">
  </a>
</p>
<p align="center">
  Generate PDF in your application.
<p>
<p align="center">
    <a href="https://github.com/zaimealabs/pdf/actions/workflows/pdf-tests.yml"><img src="https://github.com/zaimealabs/pdf/actions/workflows/pdf-tests.yml/badge.svg" alt="PDF Tests"></a>
    <a href="https://github.com/zaimealabs/pdf/blob/main/LICENSE"><img src="https://img.shields.io/badge/License-Mit-brightgreen.svg" alt="License"></a>
</p>
<div align="center">
  Hey 👋 thanks for considering making a donation, with these donations I can continue working to contribute to ZaimeaLabs projects.
  
  [![Donate](https://img.shields.io/badge/Via_PayPal-blue)](https://www.paypal.com/donate/?hosted_button_id=V6YPST5PUAUKS)
</div>

# Usage
```php
$invoice = \ZaimeaLabs\PDF\Invoice::make()
        ->addItem('Test Item', 10.25, 2, 1412)
        ->addItem('Test Item 2', 5, 2, 923)
        ->addItem('Test Item 3', 15.55, 5, 42)
        ->addItem('Test Item 4', 1.25, 1, 923)
        ->addItem('Test Item 5', 3.12, 1, 3142)
        ->addItem('Test Item 6', 5, 2, 923, 'https://dummyimage.com/64x64/000/fff')
        ->number(132) // can use our Metrics for generation, found in this package
        ->with_pagination(true)
        ->duplicate_header(true)
        ->due_date(Carbon::now()->addMonths(1))
        ->notes('Lrem ipsum dolor sit amet, consectetur adipiscing elit.')
        ->customer([
            'name'      => 'Muster Mann',
            'id'        => '231',
            'phone'     => '+49 123 456 789',
            'location'  => 'Muster Str. 1',
            'zip'       => '10115',
            'city'      => 'Muster Stadt',
            'country'   => 'Germany',
        ])
        ->download('invoice')
        //or save it somewhere
        ->save('public/pdf/myinvoice132.pdf');
```

```php
$report = \ZaimeaLabs\PDF\Report::make()
        ->addItem(now()->format('Y-m-d'), 'Custura Laurentiu', '06:00', '14:00', '00:30', '08:00', '')
            // $date, $name, $start = null, $end = null, $pause = null, $duration = null, $notes = null
        ->addChecks(true, true, false, true, true) 
            // $start, $end, $pause, $duration, $notes
        ->number(13) // can use our Metrics for generation, found in this package
        ->with_pagination(true)
        ->duplicate_header(true)
        ->due_date(Carbon::now()->addMonths(1))
        ->notes('Lrem ipsum dolor sit amet, consectetur adipiscing elit.')
        ->customer([
            'name'      => 'Muster Mann',
            'id'        => '231',
            'phone'     => '+49 123 456 789',
            'location'  => 'Muster Str. 1',
            'zip'       => '10115',
            'city'      => 'Muster Stadt',
            'country'   => 'Germany',
        ])
        ->download('report')
        //or save it somewhere
        ->save('public/pdf/myreport13.pdf');
```

Add extra fields as array to `addItem()` and `addChecks()` if you use own template
```php
->addItem(now()->format('Y-m-d'), 'Custura Laurentiu', '06:00', '14:00', '00:30', '08:00', ''
            [
                'type'          => 'worked',
                'approved'      => 'Yes',
            ]
        )

->addChecks(true, true, false, true, true,
                [
                    'type' => true,
                    'approved' => true,
                ]
            )
```

If download don't work from ->download() then can use:
```php
return response()->streamDownload( fn () => print($report->output()), 'myreport13.pdf');
```
