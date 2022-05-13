<x-admin-layout>
    <x-title-section>
        <x-slot name="title">Liste des utilisateurs</x-slot>
        <x-link-button content="Créer un compte" link="{{ route('admin.accounts.create') }}"></x-link-button>
    </x-title-section>
    <x-datatable heads="Nom, Prénom, Email, Adresse, Actif">
        @forelse ($users as $user)
            <x-datatable.tr>
                <x-datatable.td>{{ $user->nom }}</x-datatable.td>
                <x-datatable.td>{{ $user->prenom }}</x-datatable.td>
                <x-datatable.td>{{ $user->email }}</x-datatable.td>
                <x-datatable.td>{{ $user->adresse }}</x-datatable.td>
                <x-datatable.td>
                    <x-badge success="{{ $user->is_actif }}"></x-badge>
                </x-datatable.td>
                <x-datatable.td>
                    <div class="actions flex space-x-3 text-gray-600 dark:text-gray-400">
                        <x-actions.edit href="{{ route('admin.accounts.edit', ['account' => $user]) }}"></x-actions.edit>
                        <a href="{{ route('admin.accounts.password.edit', ['account' => $user->id]) }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                        </a>
                        @if(\Illuminate\Support\Facades\Auth::user()->email !== $user->email)
                            <x-actions.trash
                                route="{{ route('admin.accounts.destroy', ['account' => $user]) }}"></x-actions.trash>
                        @endif
                    </div>
                </x-datatable.td>
            </x-datatable.tr>
        @empty
            <tr>
                <td colspan="6" class="text-center">Aucune données</td>
            </tr>
        @endforelse
    </x-datatable>
</x-admin-layout>
