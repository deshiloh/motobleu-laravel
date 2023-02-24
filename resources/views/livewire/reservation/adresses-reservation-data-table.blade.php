<div>
    <div class="grid grid-cols-1 md:grid-cols-6 gap-3 mb-4">
        <div class="col-span-2">
            <x-input label="Recherche" placeholder="Tapez votre recherche..." icon="search" wire:model="search"/>
        </div>
        <div>
            <x-native-select
                label="Item par page"
                :options="['20', '50', '100', '150', '200']"
                wire:model="perPage"
                class="col-span-1"
            />
        </div>
    </div>
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
                            <x-button.circle info icon="pencil" href="{{ route('admin.adresse-reservation.edit', ['adresseReservation' => $address->id]) }}" />

                            @if(!$address->is_deleted)
                                @if($address->is_actif)
                                    <x-button.circle red icon="x" wire:click="disableAddress({{ $address }})" />
                                @else
                                    <x-button.circle green icon="check" wire:click="enableAddress({{ $address }})" />
                                @endif
                            @endif

                            @if($address->is_deleted)
                                <x-button.circle green icon="plus" wire:click="toggleDeleteAddress({{ $address }})" spinner="toggleDeleteAddress"/>
                                @else
                                <x-button.circle red icon="trash" wire:click="toggleDeleteAddress({{ $address }})" spinner="toggleDeleteAddress"/>
                            @endif
                        </div>
                    </x-datatable.td>
                </x-datatable.tr>
            @empty
                <tr>
                    <x-datatable.td class="text-center" colspan="5">
                        Aucune adresse de réservation
                    </x-datatable.td>
                </tr>
            @endforelse
        </x-slot>
    </x-datatable>
    <x-front.pagination :pagination="$adresses" :per-page="$perPage"/>
</div>
