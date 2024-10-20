<x-menu.item-link href="{{ route('admin.homepage') }}">
    Dashboard
</x-menu.item-link>
<x-menu.item-link href="{{ route('admin.accounts.index') }}">Comptes</x-menu.item-link>
<x-menu.item-link href="{{ route('admin.entreprises.index') }}">Entreprises</x-menu.item-link>
<x-menu.dropdown title="Pilotes">
    <x-menu.dropdown.item-link href="{{ route('admin.pilotes.index') }}">
        Gestion des pilotes
    </x-menu.dropdown.item-link>
    <x-menu.dropdown.item-link href="{{ route('admin.pilotes.stat-month') }}">
        Statistiques mensuelle
    </x-menu.dropdown.item-link>
    <x-menu.dropdown.item-link href="{{ route('admin.pilotes.stat-year') }}">
        Statistiques annuelle
    </x-menu.dropdown.item-link>
</x-menu.dropdown>
<x-menu.dropdown title="Gestion">
    <x-menu.dropdown.item-link href="{{ route('admin.passagers.index') }}">
        Gestion passagers
    </x-menu.dropdown.item-link>
    <x-menu.dropdown.item-link href="{{ route('admin.localisations.index') }}">
        Gestion localisations
    </x-menu.dropdown.item-link>
    <x-menu.dropdown.item-link href="{{ route('admin.adresse-reservation.index') }}">
        Gestion des adresses
    </x-menu.dropdown.item-link>
    <x-menu.dropdown.item-link href="{{ route('admin.costcenter.index') }}">
        Gestion Cost Center
    </x-menu.dropdown.item-link>
    <x-menu.dropdown.item-link href="{{ route('admin.typefacturation.index') }}">
        Gestion Type facturation
    </x-menu.dropdown.item-link>
</x-menu.dropdown>
<x-menu.item-link href="{{ route('admin.reservations.index') }}">Réservations</x-menu.item-link>
<x-menu.dropdown title="Facturation">
    <x-menu.dropdown.item-link href="{{ route('admin.facturations.index') }}">
        Liste des facturations
    </x-menu.dropdown.item-link>
    <x-menu.dropdown.item-link href="{{ route('admin.facturations.edition') }}">
        Edition des factures
    </x-menu.dropdown.item-link>
    <x-menu.dropdown.item-link href="{{ route('admin.facturations.export') }}">
        Export
    </x-menu.dropdown.item-link>
</x-menu.dropdown>
<x-menu.dropdown title="Config.">
    <x-menu.dropdown.item-link href="{{ route('admin.carousel') }}">Carousel</x-menu.dropdown.item-link>
    <x-menu.dropdown.item-link href="{{ route('admin.pages') }}">Pages</x-menu.dropdown.item-link>
    <x-menu.dropdown.item-link href="{{ route('admin.permissions') }}">Permissions</x-menu.dropdown.item-link>
    <x-menu.dropdown.item-link href="{{ route('admin.settings') }}">Paramètres</x-menu.dropdown.item-link>
</x-menu.dropdown>
<x-menu.dropdown title="Stats">
    <x-menu.dropdown.item-link href="{{ route('admin.stats.reservations') }}">
        Réservations
    </x-menu.dropdown.item-link>
    <x-menu.dropdown.item-link href="{{ route('admin.stats.facturation') }}">
        Facturation
    </x-menu.dropdown.item-link>
</x-menu.dropdown>
