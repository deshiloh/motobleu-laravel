<div class="min-h-screen bg-motobleu">
    <div class="flex items-center justify-center min-h-screen">
        <x-notifications />
        <div class="card text-neutral-content w-full max-w-xl bg-white dark:bg-slate-800 p-6 rounded-lg">
            <div class="card-body items-center">
                <div class="flex flex-col w-full border-opacity-50">
                    <div class="">
                        <div class="text-center text-2xl mb-3 dark:text-gray-100">Mot de passe oublié</div>
                        <div class="text-sm text-gray-400 mb-2 text-center">Renseignez votre adresse email et nous vous enverrons un lien pour réinitialiser votre mot de passe </div>
                        <form wire:submit.prevent="resetAction" class="w-full space-y-4">
                            <x-input
                                label="Adresse email"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-25"
                                wire:model.defer="email"
                                icon="mail"
                            >
                            </x-input>
                            <x-button
                                primary full
                                type="submit"
                                label="Envoyer"
                            />
                        </form>
                    </div>
                    <div class="relative py-4">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white dark:bg-slate-800 text-gray-500 dark:text-gray-200"> Ou </span>
                        </div>
                    </div>
                    <div>
                        <x-button primary label="Retour à l'accueil" class="w-full" href="{{ route('admin.homepage') }}" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

