<div>
    <div class="grid grid-cols-1 md:grid-cols-6 gap-3 mb-4">
        <div class="col-span-2 relative">
            <x-input label="Recherche" placeholder="Tapez votre recherche..." icon="magnifying-glass" wire:model.live.debounce.300ms="search"/>
            <div wire:loading wire:target="search" class="absolute right-2 top-8">
                <svg class="animate-spin h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </div>
        <div>
            <x-select
                :searchable="false"
                :clearable="false"
                label="Item par page"
                :options="[20, 50, 100, 150, 200]"
                wire:model.live="perPage"
                class="col-span-1"
            />
        </div>
    </div>
    <div wire:loading.class="opacity-50">
        <x-datatable>
        <x-slot name="headers">
            <tr>
                <x-datatable.th>Adresse</x-datatable.th>
                <x-datatable.th>Code postal</x-datatable.th>
                <x-datatable.th>Ville</x-datatable.th>
                <x-datatable.th>Assistante</x-datatable.th>
                <x-datatable.th>État</x-datatable.th>
                <x-datatable.th>Actions</x-datatable.th>
            </tr>
        </x-slot>
        <x-slot name="body">
            @forelse($adresses as $address)
                <x-datatable.tr>
                    <x-datatable.td>{{ $address->adresse }}</x-datatable.td>
                    <x-datatable.td>{{ $address->code_postal }}</x-datatable.td>
                    <x-datatable.td>{{ $address->ville }}</x-datatable.td>
                    <x-datatable.td>
                        {{ $address->user->full_name }}
                    </x-datatable.td>
                    <x-datatable.td>
                        @if(!$address->is_deleted)
                            <x-front.badge :success="$address->is_actif" :danger="!$address->is_actif">
                                {{ $address->is_actif ? "Actif" : "Non actif" }}
                            </x-front.badge>
                            @else
                            <x-front.badge :danger="$address->is_deleted">
                                Supprimée
                            </x-front.badge>
                        @endif
                    </x-datatable.td>
                    <x-datatable.td>
                        <div class="space-x-2">
                            <x-mini-button rounded info icon="pencil" href="{{ route('admin.adresse-reservation.edit', ['adresseReservation' => $address->id]) }}" />

                            @if(!$address->is_deleted)
                                @if($address->is_actif)
                                    <x-mini-button rounded red icon="x-mark" wire:click="disableAddress({{ $address }})" />
                                @else
                                    <x-mini-button rounded green icon="check" wire:click="enableAddress({{ $address }})" />
                                @endif
                            @endif

                            @if($address->is_deleted)
                                <x-mini-button rounded green icon="plus" wire:click="toggleDeleteAddress({{ $address }})" spinner="toggleDeleteAddress"/>
                                @else
                                <x-mini-button rounded red icon="trash" wire:click="toggleDeleteAddress({{ $address }})" spinner="toggleDeleteAddress"/>
                            @endif
                        </div>
                    </x-datatable.td>
                </x-datatable.tr>
            @empty
                <tr>
                    <x-datatable.td class="text-center" colspan="6">
                        Aucune adresse de réservation
                    </x-datatable.td>
                </tr>
            @endforelse
        </x-slot>
        </x-datatable>
    </div>
    <x-front.pagination :pagination="$adresses" :perPage="$perPage" />
</div>
