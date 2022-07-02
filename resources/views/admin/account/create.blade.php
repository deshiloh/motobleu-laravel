@php
    $selectData = [
        1 => 'Oui',
        0 => 'Non'
    ];
    $isArdian = $account ? $account->is_admin_ardian : false;
    $isActif = $account ? $account->is_actif : false;

    $entreprisesData = [];
    foreach (\App\Models\Entreprise::all() as $entreprise) {
        $entreprisesData[$entreprise->id] = $entreprise->nom;
    }
@endphp
<x-admin-layout>
    <x-title-section>
        <x-slot name="title">
            {{ $account ? "Modification de l'utilisateur ".$account->nom  : "Création d'un utilisateur"}}
        </x-slot>
    </x-title-section>
    <div class="bg-white dark:bg-gray-900 mt-3 shadow-sm p-3 rounded-lg">
        <x-form put="{{ $account != null }}" method="post"
                route="{{ $account ? route('admin.accounts.update', ['account' => $account]) : route('admin.accounts.store') }}">


        </x-form>
    </div>
</x-admin-layout>
