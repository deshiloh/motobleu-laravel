<x-layout>
    <x-header>
        Fiche de l'entreprise <span class="text-blue-700">{{ $entreprise->nom }}</span>
        <x-slot:right>
            <div class="flex space-x-2">
                @if($entreprise->adresseEntreprises()->count() < 2)
                    <x-button
                        href="{{ route('admin.entreprises.adresses.create', ['entreprise' => $entreprise]) }}"
                        sm
                        primary
                        label="Ajouter une adresse entreprise"
                    />
                @endif
                <x-button
                    href="{{ route('admin.entreprises.edit', ['entreprise' => $entreprise]) }}"
                    label="Modifier l'entreprise"
                    sm
                    primary
                />
            </div>
        </x-slot:right>
    </x-header>
    <div class="space-y-5">
        <x-bloc-content>
            <div class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">
                Responsable / Directeur : {{ $entreprise->responsable_name }}
            </div>
        </x-bloc-content>
        <div class="container mx-auto px-8">
            <div class="grid grid-cols-2 gap-5">
                <div class="dark:bg-slate-800 rounded-lg">
                    TEST
                </div>
                <div class="dark:bg-slate-800 rounded-lg p-2">
                    <livewire:entreprise.reservation-entreprise-chart :entreprise="$entreprise"/>
                </div>
            </div>
        </div>
        <x-bloc-content>
            <livewire:entreprise.users-entreprise-data-table :entreprise="$entreprise" />
        </x-bloc-content>
        <x-bloc-content>
            <livewire:entreprise.adresses-entreprise-data-table :entreprise="$entreprise"/>
        </x-bloc-content>
        <x-bloc-content>
            <livewire:entreprise.recap-reservation-entreprise :entreprise="$entreprise" />
        </x-bloc-content>
    </div>
</x-layout>
