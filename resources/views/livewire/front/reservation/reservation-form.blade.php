<div>
    <x-front.card>
        <div class="flex items-center">
            <div class="text-xl font-bold">Formulaire de réservation</div>
            <x-custom-button variant="ghost" class="ml-auto" href="{{route('front.reservation.list')}}">
                Retour à la liste
            </x-custom-button>
        </div>
        <x-errors class="mx-3"/>
    </x-front.card>
    <form wire:submit="createReservation" wire:loading.class="opacity-25" wire:key="form_reservation">
        @include('livewire.front.reservation.partials.return-toggle')
        @include('livewire.front.reservation.partials.company-selection')
        @include('livewire.front.reservation.partials.passenger-management')

        <div class="container mx-auto">
            <div class="relative mb-4">
                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center">
                    <span class="bg-gray-200 px-3 font-semibold text-gray-900 text-2xl">Course aller</span>
                </div>
            </div>
        </div>

        @include('livewire.front.reservation.partials.pickup-date')
        @include('livewire.front.reservation.partials.pickup-location')
        @include('livewire.front.reservation.partials.other-steps')
        @include('livewire.front.reservation.partials.destination-location')
        @include('livewire.front.reservation.partials.comment-submit')
        @include('livewire.front.reservation.partials.notifications')

        @if($form->hasBack)
            <div class="container mx-auto">
                <div class="relative mb-4">
                    <div class="absolute inset-0 flex items-center" aria-hidden="true">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center">
                        <span class="bg-gray-200 px-3 font-semibold text-gray-900 text-2xl">Course retour</span>
                    </div>
                </div>
            </div>

            @include('livewire.front.reservation.partials.back.pickup-date')
            @include('livewire.front.reservation.partials.back.pickup-location')
            @include('livewire.front.reservation.partials.back.other-steps')
            @include('livewire.front.reservation.partials.back.destination-location')
            @include('livewire.front.reservation.partials.back.comment')
        @endif

        <x-front.card>
            <x-custom-button type="submit">{{ __('Enregistrer') }}</x-custom-button>
        </x-front.card>
    </form>
</div>
