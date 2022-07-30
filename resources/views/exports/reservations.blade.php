<table>
    <thead>
        <tr>
            <th>Référence</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
    @foreach($reservations as $reservation)
        <tr>
            <td>{{ $reservation->reference }}</td>
            <td>{{ $reservation->pickup_date->format('d/m/Y') }}</td>
        </tr>
    @endforeach
        <tr>
            <td colspan="2" style="text-align: right;">
                <img src="{{ public_path('storage/logo-pdf.png') }}" alt="" height="20">
            </td>
        </tr>
    </tbody>
</table>
