<div>
    <x-title-section>
        <x-slot:title>
            Changement du mot de passe pour <span class="text-blue-500">{{ $user->full_name }}</span>
        </x-slot:title>
    </x-title-section>
    <x-admin.content>
        <form wire:submit.prevent="editAction" wire:loading.class="opacity-25">
            <div class="space-y-4">
                <x-input label="Mot de passe" type="password" wire:model="password" />
                <x-button label="Enregistrer" type="submit" sm info />
            </div>

        </form>
    </x-admin.content>
</div>
