<div>
    <x-front.card>
        <x-front.title>
            {{ __('Liste des Cost Center') }}
            <x-slot:button>
                <x-button primary href="{{ route('front.cost_center.create') }}" label="{{ __('Créer un Cost Center') }}" icon="plus"/>
            </x-slot:button>
        </x-front.title>
        <div class="relative">
            <x-datatable.search wire:model.live.debounce.300ms="search"/>
            <div wire:loading wire:target="search" class="absolute right-2 top-8">
                <svg class="animate-spin h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 818-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 714 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </div>
        <div wire:loading.class="opacity-50">
            <x-datatable>
            <x-slot:headers>
                <x-datatable.tr>
                    <x-datatable.th>{{ __('Label') }}</x-datatable.th>
                    <x-datatable.th>{{ __('Actif') }}</x-datatable.th>
                    <x-datatable.th>{{ __('Actions') }}</x-datatable.th>
                </x-datatable.tr>
            </x-slot:headers>
            <x-slot:body>
                @forelse($items as $item)
                    <x-datatable.tr>
                        <x-datatable.td>{{ $item->nom }}</x-datatable.td>
                        <x-datatable.td>
                            @if($item->is_actif)
                                <x-front.badge success>
                                    {{ __('Oui') }}
                                </x-front.badge>
                                @else
                                <x-front.badge danger>
                                    {{ __('Non') }}
                                </x-front.badge>
                            @endif
                        </x-datatable.td>
                        <x-datatable.td>
                            <div class="space-x-2">
                                @can('edit cost center')
                                    <x-mini-button rounded primary icon="pencil" href="{{ route('front.cost_center.edit', ['center' => $item]) }}"/>

                                    @if($item->is_actif)
                                        <x-mini-button rounded warning icon="x-mark" wire:click="toggleActifCostCenter({{ $item }})" />
                                        @else
                                        <x-mini-button rounded positive icon="check" wire:click="toggleActifCostCenter({{ $item }})" />
                                    @endif
                                @endcan

                            </div>
                        </x-datatable.td>
                    </x-datatable.tr>
                    @empty
                    <x-datatable.tr>
                        <x-datatable.td colspan="3">
                            <div class="text-center">
                                {{ __('Aucun Cost Center') }}
                            </div>
                        </x-datatable.td>
                    </x-datatable.tr>
                @endforelse
            </x-slot:body>
            </x-datatable>
        </div>
        <x-front.pagination :pagination="$items" :per-page="$perPage"/>
    </x-front.card>
</div>
