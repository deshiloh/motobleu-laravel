@props(['status'])

@php
    use App\Enum\BillStatut;

    $badgeClass = match($status) {
        BillStatut::COMPLETED => 'success',
        BillStatut::CANCEL => 'danger',
        BillStatut::CREATED => 'warning',
        default => 'secondary'
    };

    $label = match($status) {
        BillStatut::COMPLETED => 'Finalisée',
        BillStatut::CANCEL => 'Annulée',
        BillStatut::CREATED => 'En cours',
        default => 'Inconnu'
    };
@endphp

@if($badgeClass === 'success')
    <x-front.badge success>{{ $label }}</x-front.badge>
@elseif($badgeClass === 'danger')
    <x-front.badge danger>{{ $label }}</x-front.badge>
@elseif($badgeClass === 'warning')
    <x-front.badge warning>{{ $label }}</x-front.badge>
@else
    <x-front.badge>{{ $label }}</x-front.badge>
@endif