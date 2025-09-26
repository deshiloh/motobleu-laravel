<x-front.card-dark>
    <x-datetime-picker
        wire:key="back_date_picker"
        label="{{ __('Date de retour') }}"
        placeholder="{{ __('Choisir une date') }}"
        display-format="DD/MM/YYYY HH:mm"
        time-format="24"
        interval="1"
        wire:model="form.reservationBack.pickupDate"
        :without-timezone="true"
        min="{{ \Carbon\Carbon::now() }}"
    />
</x-front.card-dark>