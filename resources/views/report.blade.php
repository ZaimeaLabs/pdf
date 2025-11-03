<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>{{ $data->name }}</title>
        <style>
            * {
                -webkit-box-sizing: border-box;
                -moz-box-sizing: border-box;
                box-sizing: border-box;
            }

            h1,h2,h3,h4,h5,h6,p,span,div {
                font-family: DejaVu Sans;
                font-size:10px;
                font-weight: normal;
            }

            th,td {
                font-family: DejaVu Sans;
                font-size:10px;
            }

            .panel {
                margin-bottom: 20px;
                background-color: #fff;
                border: 1px solid transparent;
                border-radius: 4px;
                -webkit-box-shadow: 0 1px 1px rgba(0,0,0,.05);
                box-shadow: 0 1px 1px rgba(0,0,0,.05);
            }

            .panel-default {
                border-color: #ddd;
            }

            .panel-body {
                padding: 15px;
            }

            table {
                width: 100%;
                max-width: 100%;
                margin-bottom: 0px;
                border-spacing: 0;
                border-collapse: collapse;
                background-color: transparent;

            }

            thead  {
                text-align: left;
                display: table-header-group;
                vertical-align: middle;
            }

            th, td  {
                border: 1px solid #ddd;
                padding: 6px;
            }

            .well {
                min-height: 20px;
                padding: 19px;
                margin-bottom: 20px;
                background-color: #f5f5f5;
                border: 1px solid #e3e3e3;
                border-radius: 4px;
                -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.05);
                box-shadow: inset 0 1px 1px rgba(0,0,0,.05);
            }
        </style>
        @if($data->duplicate_header)
            <style>
                @page { margin-top: 140px;}
                header {
                    top: -100px;
                    position: fixed;
                }
            </style>
        @endif
    </head>
    <body>
        <header>
            <div style="position:absolute; left:0pt; width:250pt;">
                <img class="img-rounded" height="{{ $data->logo_height }}" src="{{ $data->logo }}">
            </div>
            <div style="margin-left:300pt;">
                <b>Date: </b> {{ $data->date->format('l, d M Y') }}<br />
                @if ($data->number)
                    <b>Report #: </b> {{ $data->number }}
                @endif
                <br />
            </div>
            <br />
            <h2>{{ $data->name }} {{ $data->number ? '#' . $data->number : '' }}</h2>
        </header>
        <main>
            <div style="clear:both; position:relative;">
                <div style="left:0pt; width:250pt;">
                    <h4>Business Details:</h4>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            {!! $data->business_details->count() == 0 ? '<i>No business details</i><br />' : '' !!}
                            {{ $data->business_details->get('name') }}<br />
                            ID: {{ $data->business_details->get('id') }}<br />
                            {{ $data->business_details->get('phone') }}<br />
                            {{ $data->business_details->get('location') }}<br />
                            {{ $data->business_details->get('zip') }} {{ $data->business_details->get('city') }}
                            {{ $data->business_details->get('country') }}<br />
                        </div>
                    </div>
                </div>
            </div>
            <h4>Reports:</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Name</th>
                        @if($data->checks->get('start'))
                            <th>Start</th>
                        @endif
                        @if($data->checks->get('end'))
                            <th>End</th>
                        @endif
                        @if($data->checks->get('pause'))
                            <th>Pause</th>
                        @endif
                        @if($data->checks->get('duration'))
                            <th>Duration</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data->items as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->get('date') }}</td>
                            <td>{{ $item->get('name') }}</td>
                            @if($data->checks->get('start'))
                                <td>{{ $item->get('start') }}</td>
                            @endif
                            @if($data->checks->get('end'))
                                <td>{{ $item->get('end') }}</td>
                            @endif
                            @if($data->checks->get('pause'))
                                <td>{{ $item->get('pause') }}</td>
                            @endif
                            @if($data->checks->get('duration'))
                                <td>{{ $item->get('duration') }}</td>
                            @endif
                        </tr>
                        @if ($item->get('notes') && $data->checks->get('notes'))
                            <thead>
                                <tr>
                                    <th colspan="2">{{ __('Note') }}</th>
                                    <td colspan="100%"> {{ $item->get('notes') }} </td>
                                </tr>
                            </thead>
                        @endif
                    @endforeach
                </tbody>
            </table>
            <div style="clear:both; position:relative;">
                @if($data->notes)
                    <div style="position:absolute; left:0pt; width:250pt;">
                        <h4>Notes:</h4>
                        <div class="panel panel-default">
                            <div class="panel-body">
                                {{ $data->notes }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            @if ($data->footnote)
                <br /><br />
                <div class="well">
                    {{ $data->footnote }}
                </div>
            @endif
        </main>

        <!-- Page count -->
        <script type="text/php">
            if (isset($pdf) && $GLOBALS['with_pagination'] && $PAGE_COUNT > 1) {
                $pageText = "{PAGE_NUM} of {PAGE_COUNT}";
                $pdf->page_text(($pdf->get_width()/2) - (strlen($pageText) / 2), $pdf->get_height()-20, $pageText, $fontMetrics->get_font("DejaVu Sans, Arial, Helvetica, sans-serif", "normal"), 7, array(0,0,0));
            }
        </script>
    </body>
</html>
