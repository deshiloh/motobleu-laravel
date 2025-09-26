{{-- Toujours afficher le toggle en front car pas d'édition --}}
<x-front.card>
    <div class="flex flex-col space-y-3">
        <div class="dark:text-white block">
            {{ __('Réservation avec retour') }} :
        </div>
        <div>
            <x-toggle wire:model.live="form.hasBack" left-label="{{ __('Non') }}" label="{{ __('Oui') }}" md />
        </div>
    </div>
</x-front.card>
