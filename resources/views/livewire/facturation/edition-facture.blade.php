<div>
    <x-title-section>
        <x-slot:title>
            @if($this->entreprise)
                Édition de facturation pour l'entrerise <span class="text-blue-500">{{ $this->entreprise->nom }}</span>
                @else
                Édition de facturation
            @endif
        </x-slot:title>
    </x-title-section>
    <x-admin.content>
        <div class="grid grid-cols-3 gap-4">
            <div>
                <x-select
                    label="Entreprise"
                    wire:model="entrepriseId"
                    placeholder="Sélectionner une entreprise"
                    :async-data="route('api.entreprises')"
                    option-label="nom"
                    option-value="id"
                />
            </div>
            <div>
                <x-native-select label="Mois" wire:model="model">
                    @for($i = 1; $i <= 12; $i ++)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </x-native-select>
            </div>
            <div>
                <x-native-select label="Année" wire:model="model">
                    <option value="2022">2022</option>
                </x-native-select>
            </div>
        </div>
    </x-admin.content>
</div>
