<x-guess-layout>
    <div class="flex min-h-screen flex-col justify-center py-12 sm:px-6 lg:px-8 bg-motobleu">
        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md bg-white p-6 rounded-lg">
            <h2 class="text-center text-3xl font-extrabold text-gray-200 mb-6 text-slate-900">{{ config('app.name') }}</h2>
            <form class="space-y-6" action="{{ route('login') }}" method="POST">
                @csrf
                <div>
                    <div class="mt-1">
                        <x-input label="{{ __('Adresse email') }}" placeholder="Votre adresse email..." icon="mail" name="email" value="{{ old('email') }}"/>
                    </div>
                </div>

                <div>
                    <div class="mt-1">
                        <x-input label="{{ __('Mot de passe') }}" icon="key" name="password" type="password" placeholder="Votre mot de passe..."/>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <x-checkbox id="right-label" label="{{ __('Se souvenir de moi') }}" name="remember_me" />
                    </div>

                    <div class="text-sm">
                        <a href="{{ route('password.request') }}" class="font-medium text-motobleu hover:text-indigo-500">
                            {{ __('Mot de passe oublié') }} ?
                        </a>
                    </div>
                </div>

                <div>
                    <x-button type="submit" primary label="{{ __('Se connecter') }}" full/>
                    <div class="relative py-4">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white dark:bg-slate-800 text-gray-500 dark:text-gray-200"> {{ __('Ou') }} </span>
                        </div>
                    </div>
                    <x-button secondary label="{{ __('Retour') }}" class="w-full" href="{{ route('front.home') }}" />
                </div>
            </form>
        </div>
    </div>

</x-guess-layout>
