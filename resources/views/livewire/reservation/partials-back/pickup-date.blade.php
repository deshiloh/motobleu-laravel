<x-bloc-content-dark>
    <x-datetime-picker
        wire:key="back_date_picker"
        label="Date"
        placeholder="Choisir une date"
        display-format="DD/MM/YYYY HH:mm"
        time-format="24"
        interval="1"
        wire:model="form.reservationBack.pickupDate"
        :without-timezone="true"
    />
</x-bloc-content-dark>
