<x-front.card>
    <x-datetime-picker
        wire:key="pickup_date"
        label="{{ __('Date de prise en charge') }}"
        placeholder="{{ __('Choisir une date') }}"
        parse-format="DD/MM/YYYY HH:mm"
        time-format="24"
        interval="1"
        wire:model="form.pickupDate"
        without-timezone
        min="{{ \Carbon\Carbon::now()->addMinutes(15) }}"
    />
</x-front.card>