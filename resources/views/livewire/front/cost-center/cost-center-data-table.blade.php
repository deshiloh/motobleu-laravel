<div>
    <x-front.card>
        <x-front.title>
            {{ __('Liste des Cost Center') }}
            <x-slot:button>
                <x-button primary href="{{ route('front.cost_center.create') }}" label="{{ __('Créer un Cost Center') }}" icon="plus"/>
            </x-slot:button>
        </x-front.title>
        <x-datatable.search wire:model="search"/>
        <x-datatable>
            <x-slot:headers>
                <x-datatable.tr>
                    <x-datatable.th>{{ __('Nom') }}</x-datatable.th>
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
                                <x-button.circle primary icon="pencil" href="{{ route('front.cost_center.edit', ['center' => $item]) }}"/>

                                @if($item->is_actif)
                                    <x-button.circle warning icon="x" wire:click="toggleActifCostCenter({{ $item }})" />
                                    @else
                                    <x-button.circle positive icon="check" wire:click="toggleActifCostCenter({{ $item }})" />
                                @endif

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
        <x-front.pagination :pagination="$items" :per-page="$perPage"/>
    </x-front.card>
</div>
