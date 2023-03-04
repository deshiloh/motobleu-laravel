@php
    $billSettings = app(\app\Settings\BillSettings::class);
@endphp
<div>
    <x-front.card>
        <x-front.title>
            {{ $passager->exists ? "Modification du passager" : "Création d'un passager" }}
            <x-slot:button>
                <x-button flat label="{{ __('Retour à la liste') }}" href="{{ route('front.passager.list') }}"/>
            </x-slot:button>
        </x-front.title>
        <form wire:submit.prevent="save" class="space-y-4">
            <x-input label="{!! __('Nom & prénom') !!}" wire:model.defer="passager.nom" />
            <x-input type="email" label="{{ __('Adresse email') }}" wire:model.defer="passager.email" />
            <x-input type="tel" label="{{ __('Téléphone bureau') }}" wire:model.defer="passager.telephone"/>
            <x-input label="{{ __('Téléphone portable') }}" wire:model.defer="passager.portable" />

            @if(in_array(Auth::user()->entreprises()->first()->id, $billSettings->entreprises_cost_center_facturation))
                <x-select
                    label="{{ __('Cost Center') }}"
                    placeholder="{{ __('Sélectionner un Cost Center') }}"
                    :async-data="route('api.cost_center')"
                    option-label="nom"
                    option-value="id"
                    option-description="entreprise.nom"
                    wire:model.defer="passager.cost_center_id"
                />
                <x-select
                    label="{{ __('Type Facturation') }}"
                    placeholder="{{ __('Sélectionner un type de facturation') }}"
                    :async-data="route('api.type_facturation')"
                    option-label="nom"
                    option-value="id"
                    option-description="entreprise.nom"
                    wire:model.defer="passager.type_facturation_id"
                />
            @endif
            <x-button type="submit" primary sm label="{{ __('Enregistrer') }}" wire:loading.attr="disabled" spinner="save"/>
        </form>
    </x-front.card>
</div>
