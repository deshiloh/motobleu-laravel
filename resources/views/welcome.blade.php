<x-layout>
    <div class="container bg-white max-w-lg mx-auto rounded-lg mt-10 p-5 shadow-sm">
        <h3 class="text-xl">Connexion</h3>
        <x-form route="{{ route('login') }}" method="POST">
            <x-form.input type="email" placeholder="Email" name="email"></x-form.input>
            <x-form.input type="password" placeholder="Mot de passe" name="password"></x-form.input>
            <x-form.submit></x-form.submit>
        </x-form>
    </div>
</x-layout>
