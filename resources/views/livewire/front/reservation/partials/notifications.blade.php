<x-front.card>
    <div class="flex flex-col space-y-2 my-3">
        <x-toggle wire:model="form.calendarPassengerInvitation" md
                  label="{{ __('Envoyer une invitation Google Calendar au passager') }}"/>
        <x-toggle wire:model="form.sendToPassenger" md
                  label="{!! __('Envoyer l\'email de création de la réservation au passager') !!}"/>
    </div>
</x-front.card>