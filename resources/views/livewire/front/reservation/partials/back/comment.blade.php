<x-front.card-dark>
    <div class="mb-4">
        <x-textarea label="{{ __('Commentaire course retour') }}" placeholder="{{ __('Votre commentaire...') }}"
                    wire:model="form.reservationBack.comment"/>
    </div>
</x-front.card-dark>