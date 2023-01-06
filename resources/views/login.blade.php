<x-guess-layout>
    <div class="flex min-h-screen flex-col justify-center py-12 sm:px-6 lg:px-8 bg-motobleu">
        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md bg-white p-6 rounded-lg">
            <h2 class="text-center text-3xl font-extrabold text-gray-200 mb-6 text-slate-900">{{ config('app.name') }}</h2>
            <form class="space-y-6" action="{{ route('login') }}" method="POST">
                @csrf
                <div>
                    <div class="mt-1">
                        <x-input label="Adresse email" placeholder="Votre adresse email..." icon="mail" name="email" />
                    </div>
                </div>

                <div>
                    <div class="mt-1">
                        <x-input label="Mot de passe" icon="key" name="password" type="password" placeholder="Votre mot de passe..."/>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    {{--<div class="flex items-center">
                        <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="remember-me" class="ml-2 block text-sm text-gray-900"> Remember me </label>
                    </div>--}}

                    <div class="text-sm">
                        <a href="{{ route('password.request') }}" class="font-medium text-motobleu hover:text-indigo-500"> Mot de passe oublié ? </a>
                    </div>
                </div>

                <div>
                    <x-button type="submit" primary label="Se connecter" full/>
                    <div class="relative py-4">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white dark:bg-slate-800 text-gray-500 dark:text-gray-200"> Ou </span>
                        </div>
                    </div>
                    <x-button secondary label="Retour à l'accueil" class="w-full" href="{{ route('front.home') }}" />
                </div>
            </form>
        </div>
    </div>

</x-guess-layout>
