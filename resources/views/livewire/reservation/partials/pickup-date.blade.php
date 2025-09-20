<x-bloc-content>
    <x-datetime-picker
        wire:key="pickup_date"
        label="Date de prise en charge"
        placeholder="Choisir une date"
        parse-format="DD/MM/YYYY HH:mm"
        time-format="24"
        interval="1"
        wire:model="form.pickupDate"
        without-timezone
    />
</x-bloc-content>