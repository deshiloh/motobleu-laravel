<div>
    <x-header>
        Changement du mot de passe pour <span class="text-blue-500">{{ $user->full_name }}</span>
    </x-header>
    <x-bloc-content>
        <form wire:submit.prevent="editAction" wire:loading.class="opacity-25">
            <div class="space-y-4">
                <x-input label="Mot de passe" type="password" wire:model="password" />
                <x-button type="submit" label="Enregistrer" sm primary />
            </div>
        </form>
    </x-bloc-content>
</div>
