<x-layout>
    <div class="flex flex-col items-center justify-center bg-gray-200 h-screen w-screen dark:bg-gray-700">
        <div class="container bg-white dark:bg-gray-900 max-w-lg mx-auto rounded-lg p-5 shadow-sm dark:text-gray-100">
            <h3 class="text-2xl">Changement du mot de passe</h3>
            <x-form method="post" route="{{ route('password.update') }}">
                <x-form.input type="hidden" name="token" value="{{ $token }}"></x-form.input>
                <x-form.input
                    type="email"
                    required="true"
                    label="Email"
                    name="email"
                    value="{{ $email }}"
                ></x-form.input>
                <x-form.input type="password" name="password" label="Mot de passe"></x-form.input>
            </x-form>
        </div>
    </div>
</x-layout>
