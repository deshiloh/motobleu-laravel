<div>
    <x-front.card>
        <x-front.title>
            {{ __('Liste des secrétaires') }}
            @can('create user')
                <x-slot:button>
                    <x-button primary icon="plus" label="{{ __('Ajouter une secrétaire') }}" href="{{ route('front.user.create') }}"/>
                </x-slot:button>
            @endcan
        </x-front.title>
        <x-datatable>
            <x-slot:headers>
                <x-datatable.tr>
                    <x-datatable.th>{{ __('Nom') }}</x-datatable.th>
                    <x-datatable.th>{{ __('Prénom') }}</x-datatable.th>
                    <x-datatable.th>{{ __('Actif') }}</x-datatable.th>
                    <x-datatable.th>Actions</x-datatable.th>
                </x-datatable.tr>
            </x-slot:headers>
            <x-slot:body>
                @forelse($users as $user)
                    <x-datatable.tr>
                        <x-datatable.td>{{ $user->nom }}</x-datatable.td>
                        <x-datatable.td>{{ $user->prenom }}</x-datatable.td>
                        <x-datatable.td>
                            @if($user->is_actif)
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
                                @can('edit user')
                                    <x-button.circle primary icon="pencil" href="{{ route('front.user.edit', ['account' => $user->id]) }}"/>
                                @endcan
                                @can('delete user')
                                    <x-button.circle red icon="trash" />
                                @endcan
                            </div>
                        </x-datatable.td>
                    </x-datatable.tr>
                    @empty
                    <x-datatable.tr>
                        <x-datatable.td colspan="3">
                            <div class="text-center">
                                {{ __('Aucune assistante') }}
                            </div>
                        </x-datatable.td>
                    </x-datatable.tr>
                @endforelse
            </x-slot:body>
        </x-datatable>
    </x-front.card>
</div>
