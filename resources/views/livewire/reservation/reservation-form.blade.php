<div>
    <x-header>
        Formulaire de réservation {{ $reservation->reference ?? '' }}
    </x-header>
    <div class="container mx-auto sm:px-6 lg:px-8">
        <x-errors class="mb-3"/>
    </div>
    <form wire:submit="saveReservation">
{{--        @if(!$reservation->exists)--}}
{{--            <x-bloc-content>--}}
{{--                <div class="flex flex-col space-y-3">--}}
{{--                    <div class="dark:text-white block">--}}
{{--                        Réservation avec retour :--}}
{{--                    </div>--}}
{{--                    <div>--}}
{{--                        <x-toggle wire:model.live="hasBack" left-label="Non" label="Oui" md/>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </x-bloc-content>--}}
{{--        @endif--}}
        <x-bloc-content>
            <div class="space-y-3">
{{--                <x-select--}}
{{--                    label="Secrétaire *"--}}
{{--                    placeholder="Sélectionner une secrétaire"--}}
{{--                    :async-data="route('api.users')"--}}
{{--                    option-label="full_name"--}}
{{--                    option-value="id"--}}
{{--                    option-description="entreprise.nom"--}}
{{--                    wire:model.live="selectedUser"--}}
{{--                />--}}

                <x-select
                    wire:key="cost_center"
                    label="Cost Center"
                    placeholder="Sélectionner un Cost Center"
                    :async-data="route('api.cost_center')"
                    option-label="nom"
                    option-value="id"
                    wire:model.live="selectedUser"
                />
            </div>
            {{ $this->selectedUser }}
        </x-bloc-content>
    </form>
</div>
