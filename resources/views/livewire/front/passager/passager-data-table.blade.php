<div>
    <x-front.card>
        <div class="flex border-b border-gray-200 pb-3 mb-5 justify-between items-center">
            <x-front.title>
                {{ __('Liste des passagers') }}
            </x-front.title>
            <x-button primary label="{{ __('Créer un passager') }}" icon="plus" href="{{ route('front.passager.create') }}"/>
        </div>

        <x-datatable>
            <x-slot:headers>
                <x-datatable.tr>
                    <x-datatable.th>{{ __('Nom / Prénom') }}</x-datatable.th>
                    <x-datatable.th>{{ __('Actif') }}</x-datatable.th>
                    <x-datatable.th>{{ __('Actions') }}</x-datatable.th>
                </x-datatable.tr>
            </x-slot:headers>
            <x-slot:body>
                @forelse($passagers as $passager)
                    <x-datatable.tr>
                        <x-datatable.td>{{ $passager->nom }}</x-datatable.td>
                        <x-datatable.td>
                            @if($passager->is_actif)
                                <x-front.badge success>{{ __('Oui') }}</x-front.badge>
                                @else
                                <x-front.badge danger>{{ __('Non') }}</x-front.badge>
                            @endif
                        </x-datatable.td>
                        <x-datatable.td>
                            <div class="space-x-2">
                                <x-button.circle icon="pencil" primary />
                                <x-button.circle icon="trash" red />
                            </div>
                        </x-datatable.td>
                    </x-datatable.tr>
                @empty
                    <x-datatable.tr>
                        <x-datatable.td>
                            {{ __('Aucun passager') }}
                        </x-datatable.td>
                    </x-datatable.tr>
                @endforelse

            </x-slot:body>
        </x-datatable>
    </x-front.card>
</div>
