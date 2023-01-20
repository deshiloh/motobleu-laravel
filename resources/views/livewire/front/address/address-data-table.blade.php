<div>
    <x-front.card>
        <x-front.title>
            {{ __('Liste des adresses') }}
            <x-slot:button>
                @can('create address reservation')
                    <x-button primary href="{{ route('front.address.create') }}" label="{{ __('Créer une adresse') }}" icon="plus"/>
                @endcan
            </x-slot:button>
        </x-front.title>
        <x-datatable.search wire:model="search"/>
        <x-datatable>
            <x-slot:headers>
                <x-datatable.tr>
                    <x-datatable.th>{{ __('Adresse') }}</x-datatable.th>
                    <x-datatable.th>{{ __('Actif') }}</x-datatable.th>
                    <x-datatable.th>{{ __('Actions') }}</x-datatable.th>
                </x-datatable.tr>
            </x-slot:headers>
            <x-slot:body>
                @forelse($addresses as $address)
                    <x-datatable.tr>
                        <x-datatable.td>{{ $address->adresse }}</x-datatable.td>
                        <x-datatable.td>
                            @if($address->is_actif)
                                <span class="bg-green-100 text-green-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded dark:bg-green-200 dark:text-green-900">{{ __('Oui') }}</span>
                                @else
                                <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">Non</span>
                            @endif
                        </x-datatable.td>
                        <x-datatable.td>
                            <div class="space-x-2">
                                @can('edit address reservation')
                                    <x-button.circle primary icon="pencil" href="{{ route('front.address.edit', ['address' => $address->id]) }}"/>


                                    @if($address->is_actif)
                                        <x-button.circle warning icon="x" wire:click="toggleAddress({{ $address }})"/>
                                        @else
                                        <x-button.circle positive icon="check" wire:click="toggleAddress({{ $address }})"/>
                                    @endif
                                @endcan

                                @can('delete address reservation')
                                    <x-button.circle red icon="trash" wire:click="deleteAddress({{ $address }})"/>
                                @endcan
                            </div>
                        </x-datatable.td>
                    </x-datatable.tr>
                @empty
                        <x-datatable.tr>
                            <x-datatable.td colspan="2">{{ __('Aucune adresse') }}</x-datatable.td>
                        </x-datatable.tr>
                @endforelse
            </x-slot:body>
        </x-datatable>
        <x-front.pagination :pagination="$addresses" :per-page="$perPage" />
    </x-front.card>
</div>
