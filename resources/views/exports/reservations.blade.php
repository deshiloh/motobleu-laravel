<table>
    <thead>
    <tr>
        <th>Référence</th>
        <th>Date</th>
    </tr>
    </thead>
    <tbody>
    @foreach($reservations as $reservation)
        <tr style="background-color: red;">
            <td>{{ $reservation->reference }}</td>
            <td>{{ $reservation->pickup_date->format('d/m/Y') }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
