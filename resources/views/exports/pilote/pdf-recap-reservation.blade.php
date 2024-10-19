@php
    $currentDate = $period[1];
    $fmt = new NumberFormatter('fr_FR', NumberFormatter::CURRENCY);
    $totalEncaisse = 0;
    $totalEncompte = 0;
    $totalCom = 0;
@endphp
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-15">
    <style>
        @page {
            margin: 0.25cm 0.25cm;
            size: 279mm 205mm;
        }

        /** Define now the real margins of every page in the PDF **/
        body {
            margin-top: 10px;
            margin-bottom: 10px;
            font-family: 'Roboto', sans-serif;
            padding-top: 2.1cm;
            padding-bottom: 2.5cm;
            font-size: 13px;
        }

        .body {
            margin-left: 2cm;
            margin-right: 2cm;
        }
        /** Define the header rules **/
        header {
            position: fixed;
            top: 0cm;
            left: 0cm;
            right: 0cm;
            height: 2.1cm;

            /** Extra personal styles **/
            background-color: #293275;
            color: white;
            text-align: center;
        }

        header table {
            z-index: 1000;
        }
        .header-bg {
            position: absolute;
            top: -1cm;
            left: 0;
            right: 0;
            z-index: 1;
        }

        /** Define the footer rules **/
        footer {
            position: fixed;
            bottom: 0cm;
            left: 0cm;
            right: 0cm;
            height: 1cm;

            /** Extra personal styles **/
            background-color: #293275;
            /*background-image: url(FOOTER); */
            background-repeat: no-repeat;
            background-position: bottom center;
            color: white;
            text-align: center;
            font-size: 0.6em;

            display: flex;
            justify-content: center;
            padding-top: 12px;
            padding-bottom: 20px;
        }

        #title {
            padding: 20px 10px;
            font-size: 14px;
        }

        .listing {
            border-spacing: 0;
            border: 0;
            width: 100%;
        }

        .listing thead{
            background-color: #293275;
            text-align: center;
            color: white;
            border-bottom: 1px solid black;
        }

        .listing thead td {
            border-right: 1px solid black;
            padding: 3px;
        }

        .listing tbody td {
            text-align: center;
            border-left: 1px solid black;
            border-bottom: 1px solid black;
            padding: 3px;
        }

        .listing tbody td:last-child {
            border-right: 1px solid black;
        }

        .odd {
            background-color: #dbdbdb;
        }

        .bg-motobleu {
            background-color: #293275;
        }

        .recap-end {
            font-weight: bold;
        }
    </style>
    <title></title>
</head>
<body>
<header>
    <table>
        <tr>
            <td>
                <img src="{{ storage_path('app/public/logo-pdf.png') }}"
                     alt="Motobleu Paris"
                     width="200" style="padding-left: 30px; padding-top: 12px;"
                >
            </td>
        </tr>
    </table>
</header>
<div id="title">
    Tableau recap courses <br>
    {{ $pilote->full_name }} / {{ $pilote->email }} / {{ $currentDate->isoFormat('MMMM') . ' ' . $currentDate->format('Y') }}
</div>
<footer>
    <div>
        MOTOBLEU<br>
        26 - 28 RUE MARIUS AUFAN 92300 LEVALLOIS PERRET<br>
        SIRET: 82472195500014 - TVA intracommunautaire: FR69824721955<br>
        Tél:+33647938617 - contact@motobleu-paris.com
    </div>
</footer>
<table class="listing">
    <thead>
        <tr>
            <td>Date</td>
            <td>Heure</td>
            <td>Passager</td>
            <td>Départ</td>
            <td>Arrivée</td>
            <td>Commentaire</td>
            <td>Encaisse</td>
            <td>Encompte</td>
            <td>Course N°</td>
            <td>COM</td>
        </tr>
    </thead>
    <tbody>
        @foreach($reservations as $reservation)
            @php
                $resaComm = $reservation->commission ? $reservation->commission : $pilote->commission;

                $price = ($reservation->encaisse_pilote + $reservation->encompte_pilote) * ($resaComm / 100);
                $totalCom += $price;
                $totalEncaisse = $reservation->encaisse_pilote + $totalEncaisse;
                $totalEncompte = $reservation->encompte_pilote + $totalEncompte;
            @endphp
            <tr @if($loop->odd)class="odd"@endif >
                <td>
                    {{ $reservation->pickup_date->format('d/m/Y') }}
                </td>
                <td>
                    {{ $reservation->pickup_date->format('H:i') }}
                </td>
                <td>
                    {{ $reservation->passager->nom }}
                </td>
                <td>
                    {{ $reservation->display_from }}
                </td>
                <td>
                    {{ $reservation->display_to }}
                </td>
                <td>
                    {{ $reservation->comment_pilote }}
                </td>
                <td>
                    {{ $fmt->formatCurrency($reservation->encaisse_pilote, 'EUR') }}
                </td>
                <td>
                    {{ $fmt->formatCurrency($reservation->encompte_pilote, 'EUR') }}
                </td>
                <td>
                    {{ $reservation->reference }}
                </td>
                <td>
                    {{ $reservation->commission ?? $reservation->pilote->commission}}
                </td>
            </tr>
        @endforeach

        <tr class="recap-end">
            <td>
                Chiffre d'affaires
            </td>
            <td class="bg-motobleu"></td>
            <td>
                COM
            </td>
            <td class="bg-motobleu"></td>
            <td>
                ENCAISSE
            </td>
            <td class="bg-motobleu"></td>
            <td>
                EN COMPTE
            </td>
            <td class="bg-motobleu"></td>
            <td style="color: red">
                TOTAL
            </td>
            <td class="bg-motobleu"></td>
        </tr>
        @php
            $ca = $totalEncaisse + $totalEncompte;
            $total = $totalEncompte - $totalCom ;
        @endphp
        <tr class="recap-end">
            <td>
                {{ number_format($ca, 2, ',', ' ') . ' €' }}
            </td>
            <td class="bg-motobleu"></td>
            <td>
                {{ number_format($totalCom, 2, ',', ' ') . ' €' }}
            </td>
            <td class="bg-motobleu"></td>
            <td>
                {{ number_format($totalEncaisse, 2, ',', ' ') . ' €' }}
            </td>
            <td class="bg-motobleu"></td>
            <td>
                {{ number_format($totalEncompte, 2, ',', ' ') . ' €' }}
            </td>
            <td class="bg-motobleu"></td>
            <td style="color: red">
               {{ number_format($total, 2, ',', ' ') . ' €' }}
            </td>
            <td class="bg-motobleu"></td>
        </tr>
    </tbody>
</table>

</body>
</html>
