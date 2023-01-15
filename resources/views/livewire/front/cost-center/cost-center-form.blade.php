<div>
    <x-front.card>
        <x-front.title>
            {!! $contextNewCostCenter ? __('Création d\'un Cost Center') : __('Modification du Cost Center') !!}
            <x-slot:button>
                <x-button flat href="{{ route('front.cost_center.list') }}" label="{{ __('Retour à la liste') }}"/>
            </x-slot:button>
        </x-front.title>
        <x-errors class="mb-2"/>
        <form wire:submit.prevent="save" class="space-y-3">
            <x-input label="{{ __('Nom') }}" wire:model.defer="costCenter.nom" />
            <x-toggle wire:model.defer="costCenter.is_actif" md label="{{ __('Actif') }}" />
            <x-button type="submit" primary sm label="{{ __('Enregistrer') }}" wire:loading.attr="disabled"/>
        </form>
    </x-front.card>
</div>
