<div class="bg-base-200 min-h-screen flex items-center justify-center">
    <x-notifications />
    <div class="card bg-base-100 text-neutral-content w-full max-w-xl">
        <div class="card-body items-center">

            <div class="flex flex-col w-full border-opacity-50">
                <div class="">
                    <div class="text-center text-2xl mb-3">Mot de passe oublié</div>
                    <div class="text-sm text-gray-400 mb-2">Renseignez votre adresse email et nous vous enverrons un lien pour réinitialiser votre mot de passe </div>
                    <form wire:submit.prevent="resetAction" class="w-full">
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
                                        ghost
                                        type="submit"
                                    />
                                </div>
                            </x-slot>
                        </x-input>
                    </form>
                </div>
                <div class="divider">OU</div>
                <div>
                    <x-button label="Retour à l'accueil" class="w-full" href="{{ route('homepage') }}" />
                </div>
            </div>
        </div>
    </div>

    {{--<div class="flex flex-col items-center justify-center h-screen w-screen">
        <div class="container max-w-lg mx-auto rounded-lg p-5 shadow-sm dark:text-gray-100">

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
                                primary
                                type="submit"
                            />
                        </div>
                    </x-slot>
                </x-input>
            </form>
            <div class="flex items-center my-7 before:flex-1 before:border-t before:border-gray-300 before:mt-0.5 after:flex-1 after:border-t after:border-gray-300 after:mt-0.5">
                <p class="text-center font-semibold mx-4 mb-0">Ou</p>
            </div>

        </div>
    </div>--}}
</div>
