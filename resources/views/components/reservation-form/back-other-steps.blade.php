<div class="flex flex-col space-y-3">
    <div class="dark:text-white block">
        {{ __('Ajouter une ou plusieurs destinations intermédiaires') }} :
    </div>
    <div>
        <x-toggle wire:model="reservation_back.has_steps" left-label="{{ __('Non') }}" label="{{ __('Oui') }}" md/>
    </div>

    @if($reservation_back->has_steps)
        <x-textarea placeholder="{{ __('Ex : S\'arrêter au domicile du client au 33 avenue de Turenne à Vincennes') }}" wire:model.defer="reservation_back.steps" label="{{ __('Indiquez ici la ou les destinations intermédiaires :') }}" />
    @endif
</div>
