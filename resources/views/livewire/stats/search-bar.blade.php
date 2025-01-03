<div>
    <x-bloc-content>
        <div class="grid grid-cols-3 gap-5">
            <x-native-select
                label="Année"
                :options="$years"
                wire:model.live="selectedYear"
            />
            <x-select
                label="Entreprise"
                placeholder="Sélectionner une entreprise"
                :async-data="route('api.entreprises')"
                option-label="nom"
                option-value="id"
                wire:model.live="selectedEntreprise"
            />
        </div>
    </x-bloc-content>
</div>
