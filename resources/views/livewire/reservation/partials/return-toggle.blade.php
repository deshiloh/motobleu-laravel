@if(!$reservation->exists)
    <x-bloc-content>
        <div class="flex flex-col space-y-3">
            <div class="dark:text-white block">
                Réservation avec retour :
            </div>
            <div>
                <x-toggle wire:model.live="form.hasBack" left-label="Non" label="Oui" md />
            </div>
        </div>
    </x-bloc-content>
@endif