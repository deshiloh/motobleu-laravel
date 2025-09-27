@props(['model' => 'entreprise', 'label' => 'Entreprise'])

<x-select
    :label="$label"
    wire:model.live="{{ $model }}"
    placeholder="Rechercher une entreprise"
    :async-data="route('api.entreprises')"
    option-label="nom"
    option-value="id"
    wire:change="$refresh"
    {{ $attributes }}
/>