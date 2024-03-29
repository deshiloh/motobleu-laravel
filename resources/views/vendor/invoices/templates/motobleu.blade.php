@php
    $billSettings = app(\app\Settings\BillSettings::class);
@endphp
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>{{ $invoice->name }}</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

        <style type="text/css" media="screen">
            html {
                font-family: sans-serif;
                line-height: 1.15;
                margin: 0;
            }

            body {
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
                font-weight: 400;
                line-height: 1.5;
                color: #212529;
                text-align: left;
                background-color: #fff;
                font-size: 10px;
                margin: 36pt;
            }

            h4 {
                margin-top: 0;
                margin-bottom: 0.5rem;
            }

            p {
                margin-top: 0;
                margin-bottom: 1rem;
            }

            strong {
                font-weight: bolder;
            }

            img {
                vertical-align: middle;
                border-style: none;
            }

            table {
                border-collapse: collapse;
            }

            th {
                text-align: inherit;
            }

            h4, .h4 {
                margin-bottom: 0.5rem;
                font-weight: 500;
                line-height: 1.2;
            }

            h4, .h4 {
                font-size: 1.5rem;
            }

            .table {
                width: 100%;
                margin-bottom: 1rem;
                color: #212529;
            }

            .table th,
            .table td {
                padding: 0.75rem;
                vertical-align: top;
            }

            .table.table-items td {
                border-top: 1px solid #dee2e6;
            }

            .table thead th {
                vertical-align: bottom;
                border-bottom: 2px solid #dee2e6;
            }

            .mt-5 {
                margin-top: 3rem !important;
            }

            .pr-0,
            .px-0 {
                padding-right: 0 !important;
            }

            .pl-0,
            .px-0 {
                padding-left: 0 !important;
            }

            .text-right {
                text-align: right !important;
            }

            .text-center {
                text-align: center !important;
            }

            .text-uppercase {
                text-transform: uppercase !important;
            }
            * {
                font-family: "DejaVu Sans";
            }
            body, h1, h2, h3, h4, h5, h6, table, th, tr, td, p, div {
                line-height: 1.1;
            }
            .party-header {
                font-size: 1.5rem;
                font-weight: 400;
            }
            .total-amount {
                font-size: 12px;
                font-weight: 700;
            }
            .border-0 {
                border: none !important;
            }
            .cool-gray {
                color: #6B7280;
            }

            .table-items thead th {
                background-color: #09158D;
                color: white;
                padding-right: 10px;
                padding-left: 10px !important;
                border: 0 #09158D;
            }

            .invoice-item {
                background-color: #e3e3e3;
            }

            .invoice-item td {
                padding-left: 10px !important;
            }

            .invoice-note-content {
                margin-top: 15px;
            }

            .invoice-note-content p {
                margin: 0;
                padding: 0;
                line-height: 2px;
            }
        </style>
    </head>

    <body style="border-bottom: 1px solid black; position: relative;">
        {{-- Header --}}
        @if($invoice->logo)
            <img src="{{ $invoice->getLogo() }}" alt="logo" height="80">
        @endif

        <table class="table mt-5">
            <tbody>
                <tr>
                    <td class="border-0 pl-0" width="70%">
                        <h4 class="text-uppercase">
                            <span style="font-size: 10pt;">Facture N° :</span> <br>
                            <strong>{{ $invoice->name }}</strong>
                            @if($invoice->status)
                                <h4 class="text-uppercase cool-gray">
                                    <strong>{{ $invoice->status }}</strong>
                                </h4>
                            @endif
                        </h4>
                    </td>
                    <td class="border-0 pl-0">
{{--                        <p>{{ __('invoice.serial') }} <strong>{{ $invoice->getSerialNumber() }}</strong></p>--}}
                        <p>{{ __('invoice.date') }}: <strong>{{ $invoice->getDate() }}</strong></p>
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- Seller - Buyer --}}
        <table class="table">
            <thead>
                <tr>
                    <th class="border-0 pl-0 party-header" width="48.5%">
                        {{ __('invoice.customer_address') }}
                    </th>
                    <th class="border-0" width="3%"></th>
                    <th class="border-0 pl-0 party-header">
                        {{ __('invoice.invoice_address') }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="px-0">
                        @if($invoice->seller->name)
                            <p class="seller-name">
                                <strong>{{ $invoice->seller->name }}</strong>
                            </p>
                        @endif

                        @if($invoice->seller->address)
                            <p class="seller-address">
                                {!! $invoice->seller->address !!}
                            </p>
                        @endif

                        @if($invoice->seller->code)
                            <p class="seller-code">
                                {{ __('invoice.code') }}: {{ $invoice->seller->code }}
                            </p>
                        @endif

                        @if($invoice->seller->vat)
                            <p class="seller-vat">
                                {{ __('invoice.vat') }}: {{ $invoice->seller->vat }}
                            </p>
                        @endif

                        @if($invoice->seller->phone)
                            <p class="seller-phone">
                                {{ __('invoice.phone') }}: {{ $invoice->seller->phone }}
                            </p>
                        @endif

                        @foreach($invoice->seller->custom_fields as $key => $value)
                            <p class="seller-custom-field">
                                {{ ucfirst($key) }}: {{ $value }}
                            </p>
                        @endforeach
                    </td>
                    <td class="border-0"></td>
                    <td class="px-0">
                        @if($invoice->buyer->name)
                            <p class="buyer-name">
                                <strong>{{ $invoice->buyer->name }}</strong>
                            </p>
                        @endif

                        @if($invoice->buyer->address)
                            <p class="buyer-address">
                                {!! $invoice->buyer->address !!}
                            </p>
                        @endif

                        @if($invoice->buyer->code)
                            <p class="buyer-code">
                                {{ __('invoice.code') }}: {{ $invoice->buyer->code }}
                            </p>
                        @endif

                        @if($invoice->buyer->vat)
                            <p class="buyer-vat">
                                {{ __('invoice.vat') }}: {{ $invoice->buyer->vat }}
                            </p>
                        @endif

                        @if($invoice->buyer->phone)
                            <p class="buyer-phone">
                                {{ __('invoice.phone') }}: {{ $invoice->buyer->phone }}
                            </p>
                        @endif

                        @foreach($invoice->buyer->custom_fields as $key => $value)
                            <p class="buyer-custom-field">
                                {{ ucfirst($key) }}: {{ $value }}
                            </p>
                        @endforeach
                    </td>
                </tr>
            </tbody>
        </table>

        @php
            $data = $invoice->getCustomData();
            $ttc = $data['montant_ttc'];
            $prixHT = $ttc / 1.10;
            $prixTVA = $ttc - $prixHT;
        @endphp

        {{-- Table --}}
        <table class="table table-items" border="0">
            <thead>
                <tr>
                    <th scope="col" class="border-0 pl-0">{{ __('invoice.description') }}</th>
                    @if($invoice->hasItemUnits)
                        <th scope="col" class="text-center border-0">{{ __('invoice.units') }}</th>
                    @endif

                    @if($invoice->hasItemDiscount)
                        <th scope="col" class="text-right border-0">{{ __('invoice.discount') }}</th>
                    @endif
                    @if($invoice->hasItemTax)
                        <th scope="col" class="text-right border-0">{{ __('invoice.tax') }}</th>
                    @endif
                    <th scope="col" class="text-right border-0">{{ __('invoice.period') }}</th>
                    <th scope="col" class="text-right border-0">{{ __('invoice.price') }}</th>
                </tr>
            </thead>
            <tbody>
                {{-- Items --}}
                @foreach($invoice->items as $item)
                    <tr class="invoice-item">
                        <td class="pl-0">
                            {{ $item->title }}
                        </td>
                        @if($invoice->hasItemUnits)
                            <td class="text-center">{{ $item->units }}</td>
                        @endif
                        {{--<td class="text-center">{{ $item->quantity }}</td>--}}

                        @if($invoice->hasItemDiscount)
                            <td class="text-right">
                                {{ $invoice->formatCurrency($item->discount) }}
                            </td>
                        @endif
                        @if($invoice->hasItemTax)
                            <td class="text-right">
                                {{ $invoice->formatCurrency($item->tax) }}
                            </td>
                        @endif

                        <td class="text-right">
                            @if($item->description)
                                <p>{{ $item->description }}</p>
                            @endif
                        </td>
                        <td class="text-right">
                            {{ number_format($item->price_per_unit, 2, ',', ' ') }}
                        </td>
                    </tr>
                @endforeach
                {{-- Summary --}}
                @if($invoice->hasItemOrInvoiceDiscount())
                    <tr>
                        <td colspan="{{ $invoice->table_columns - 1 }}" class="border-0"></td>
                        <td class="text-right pl-0">{{ __('invoice.total_discount') }}</td>
                        <td class="text-right pr-0">
                            {{ $invoice->formatCurrency($invoice->total_discount) }}
                        </td>
                    </tr>
                @endif
                @if($invoice->taxable_amount)
                    <tr>
                        <td colspan="{{ $invoice->table_columns - 2 }}" class="border-0"></td>
                        <td class="text-right pl-0">{{ __('invoice.taxable_amount') }}</td>
                        <td class="text-right pr-0">
                            {{ number_format($prixHT, 2, ',', ' ') }} €
                        </td>
                    </tr>
                @endif
{{--                @if($invoice->tax_rate)--}}
{{--                    <tr>--}}
{{--                        <td colspan="{{ $invoice->table_columns - 2 }}" class="border-0"></td>--}}
{{--                        <td class="text-right pl-0">{{ __('invoice.tax_rate') }}</td>--}}
{{--                        <td class="text-right pr-0">--}}
{{--                            {{ $invoice->tax_rate }}%--}}
{{--                        </td>--}}
{{--                    </tr>--}}
{{--                @endif--}}
                @if($invoice->hasItemOrInvoiceTax())
                    <tr>
                        <td colspan="{{ $invoice->table_columns - 2 }}" class="border-0"></td>
                        <td class="text-right pl-0">{{ __('invoice.total_taxes') }} ({{ $invoice->tax_rate }} %)</td>
                        <td class="text-right pr-0">
                            {{ number_format($prixTVA, 2, ',', ' ') }} €
                        </td>
                    </tr>
                @endif
                @if($invoice->shipping_amount)
                    <tr>
                        <td colspan="{{ $invoice->table_columns - 2 }}" class="border-0"></td>
                        <td class="text-right pl-0">{{ __('invoice.shipping') }}</td>
                        <td class="text-right pr-0">
                            {{ $invoice->formatCurrency($invoice->shipping_amount) }}
                        </td>
                    </tr>
                @endif
                    <tr>
                        <td colspan="{{ $invoice->table_columns - 2 }}" class="border-0"></td>
                        <td class="text-right pl-0" style="font-weight: bold; font-size: 10pt;">{{ __('invoice.total_amount') }}</td>
                        <td class="text-right pr-0 total-amount">
                            {{ number_format($ttc, 2, ',', ' ') }} €
                        </td>
                    </tr>
            </tbody>
        </table>

        <div style="margin-top: 70px;" class="invoice-note">
            @if($invoice->notes)
                <span style="font-weight: bold">{{ trans('invoice.notes') }}:</span>
                <div class="invoice-note-content">
                    {!! nl2br($invoice->notes) !!}
                </div>
            @endif
        </div>

        {{-- Footer --}}
        <table style="position: absolute; bottom: 110;">
            <tr>
                <td>
                    <p>
                        MOTOBLEU <br>
                        26 - 28 RUE MARIUS AUFAN 92300 LEVALLOIS PERRET <br>
                        SIRET : 82472195500014 - TVA intracommunautaire : FR69824721955 Tél : +33647938617 - contact@motobleu-paris.com
                    </p>
                </td>
                <td>
                    <p style="margin-bottom: -5; padding-left: 23">
                        CONDITIONS DE PAIEMENT :
                    </p>
                    <ul >
                        <li>Pas d'escompte si règlement anticipé.</li>
                        <li>Toute somme non payée dans les 30 jours est augmentée du taux d'intérêt légal majoré de 7 points.</li>
                        <li>Indemnité forfaitaire de recouvrement : 40 euros</li>
                    </ul>
                </td>
            </tr>
            <tr>
                <td style="text-align: center;" colspan="2">
                    {!! nl2br($billSettings->rib) !!}
                </td>
            </tr>
        </table>

        {{--<p>
            {{ trans('invoice.amount_in_words') }}: {{ $invoice->getTotalAmountInWords() }}
        </p>--}}
        {{--<p>
            {{ trans('invoice.pay_until') }}: {{ $invoice->getPayUntilDate() }}
        </p>--}}

        <script type="text/php">
            if (isset($pdf) && $PAGE_COUNT > 1) {
                $text = "Page {PAGE_NUM} / {PAGE_COUNT}";
                $size = 10;
                $font = $fontMetrics->getFont("Verdana");
                $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
                $x = ($pdf->get_width() - $width);
                $y = $pdf->get_height() - 35;
                $pdf->page_text($x, $y, $text, $font, $size);
            }
        </script>
    </body>
</html>
