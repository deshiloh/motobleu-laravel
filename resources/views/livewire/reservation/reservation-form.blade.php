<div>
    <x-header>
        Formulaire de réservation {{ $reservation->reference ?? '' }}
    </x-header>
    <div class="container mx-auto sm:px-6 lg:px-8">
        <x-errors class="mb-3"/>
    </div>
    <form wire:submit="saveReservation" wire:loading.class="opacity-25" wire:key="form_reservation">
        @include('livewire.reservation.partials.return-toggle')

        @include('livewire.reservation.partials.secretary-company')

        @include('livewire.reservation.partials.passenger-management')

        <div class="md:px-4">
            <div class="relative mb-4">
                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center">
                    <span class="bg-gray-100 px-3 font-semibold text-gray-900 text-2xl">Course aller</span>
                </div>
            </div>
        </div>

        @include('livewire.reservation.partials.pickup-date')

        @include('livewire.reservation.partials.pickup-location')

        <x-bloc-content>
            @include('components.reservation-form.other-steps')
        </x-bloc-content>

        @include('livewire.reservation.partials.destination-location')

        @include('livewire.reservation.partials.comment-submit')

        @if($form->hasBack)
            <div class="md:px-4">
                <div class="relative mb-4">
                    <div class="absolute inset-0 flex items-center" aria-hidden="true">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center">
                        <span class="bg-gray-100 px-3 font-semibold text-gray-900 text-2xl">Course retour</span>
                    </div>
                </div>
            </div>

            @include('livewire.reservation.partials-back.pickup-date')

            @include('livewire.reservation.partials-back.pickup-location')

            <x-bloc-content-dark>
                @include('components.reservation-form.back-other-steps')
            </x-bloc-content-dark>

            @include('livewire.reservation.partials-back.destination-location')

            @include('livewire.reservation.partials-back.comment-submit')
        @endif

        <x-bloc-content>
            <div class="flex flex-col space-y-2 my-3">
                <x-toggle wire:model="form.calendarPassengerInvitation" md
                          label="{{ __('Envoyer une invitation Google Calendar au passager') }}"/>
                <x-toggle wire:model="form.sendToPassenger" md
                          label="{!! __('Envoyer l\'email de création de la réservation au passager') !!}"/>
            </div>
        </x-bloc-content>

        <x-bloc-content>
            <x-custom-button type="submit">Enregistrer</x-custom-button>
        </x-bloc-content>
    </form>

{{--    <x-modal blur wire:model.defer="form.ardianPassengerCostFacError">--}}
{{--        <x-card title="Édition du passanger">--}}
{{--            @if($form->passengerInError)--}}
{{--                <form id="test" wire:submit.prevent="savePassenger" method="post">--}}
{{--                    <div class="space-y-3">--}}
{{--                        <div>--}}
{{--                            Passager : {{ $form->passengerInError->nom }}--}}
{{--                        </div>--}}
{{--                        <x-select--}}
{{--                            wire:key="cost_center_exist_passenger"--}}
{{--                            label="Cost Center"--}}
{{--                            placeholder="Sélectionner un Cost Center"--}}
{{--                            :async-data="route('api.cost_center')"--}}
{{--                            option-label="nom"--}}
{{--                            option-value="id"--}}
{{--                            wire:model="form.passengerInError.cost_center_id"--}}
{{--                        />--}}
{{--                        <x-select--}}
{{--                            wire:key="type_facturation_exist_passenger"--}}
{{--                            label="Type de facturation"--}}
{{--                            placeholder="Sélectionner un type de facturation"--}}
{{--                            :async-data="route('api.type_facturation')"--}}
{{--                            option-label="nom"--}}
{{--                            option-value="id"--}}
{{--                            wire:model="form.passengerInError.type_facturation_id"--}}
{{--                        />--}}
{{--                    </div>--}}
{{--                </form>--}}
{{--            @endif--}}
{{--            <x-slot name="footer">--}}
{{--                <div class="flex justify-end gap-x-4">--}}
{{--                    <x-button flat label="Annuler" x-on:click="close" />--}}
{{--                    <x-button primary label="Enregistrer" type="submit" form="test"/>--}}
{{--                </div>--}}
{{--            </x-slot>--}}
{{--        </x-card>--}}
{{--    </x-modal>--}}
</div>
