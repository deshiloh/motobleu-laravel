<div>
    <x-notifications />
    <div class="flex flex-col items-center justify-center bg-gray-200 h-screen w-screen dark:bg-gray-800">
        <div class="container bg-white dark:bg-gray-900 max-w-lg mx-auto rounded-lg p-5 shadow-sm dark:text-gray-100">
            <div class="text-center text-xl mb-3">Mot de passe oublié</div>
            <div class="text-sm text-center text-gray-400 mb-4">Renseignez votre adresse email et nous vous enverrons un lien pour réinitialiser votre mot de passe </div>
            <form wire:submit.prevent="resetAction">
                <x-input
                    label="Adresse email"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-25"
                    wire:model.defer="email"
                    icon="mail"
                >
                    <x-slot name="append">
                        <div class="absolute inset-y-0 right-0 flex items-center p-0.5">
                            <x-button
                                class="h-full rounded-r-md"
                                icon="arrow-right"
                                info
                                flat
                                squared
                                type="submit"
                            />
                        </div>
                    </x-slot>
                </x-input>
            </form>
            <div class="flex items-center my-7 before:flex-1 before:border-t before:border-gray-300 before:mt-0.5 after:flex-1 after:border-t after:border-gray-300 after:mt-0.5">
                <p class="text-center font-semibold mx-4 mb-0">Ou</p>
            </div>
            <x-button label="Retour à l'accueil" class="w-full" outline info href="{{ route('homepage') }}" />
        </div>
    </div>
</div>
