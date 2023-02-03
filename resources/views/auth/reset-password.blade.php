<x-guess-layout>
    <div class="h-screen bg-motobleu flex items-center justify-center">
        <div class="container bg-white dark:bg-gray-900 max-w-lg mx-auto rounded-lg p-5 shadow-sm dark:text-gray-100">
            <h3 class="text-2xl text-center">{{ __('Changement du mot de passe') }}</h3>
            <div class="w-full space-x-2 text-center py-3">
                <a href="{{ route('switch.local', ['locale' => 'fr']) }}"><span class="fi fi-fr"></a>
                <a href="{{ route('switch.local', ['en']) }}"><span class="fi fi-gb"></a>
            </div>
            <x-errors />
            <form action="{{ route('password.update') }}" method="post" class="space-y-3">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <x-input type="email" label="Email" value="{{ $email }}" name="email"/>
                <x-input type="password" label="{{ __('Mot de passe') }}" name="password"/>
                <x-input type="password" label="{{ __('Confirmer le mot de passe') }}" name="password_confirmation"/>
                <x-button type="submit" label="{{ __('Envoyer') }}" primary full />
            </form>
        </div>
    </div>
</x-guess-layout>
