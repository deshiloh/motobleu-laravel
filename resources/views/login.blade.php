<x-front-layout>
    <div class="min-h-screen">
        <div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
            <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
                <div class="bg-white dark:bg-slate-800 py-8 px-4 shadow sm:rounded-lg sm:px-10">
                    <div class="flex justify-center py-2">
                        <x-darkmode :size="6"/>
                    </div>
                    <h2 class="text-center text-3xl font-extrabold text-gray-900 dark:text-gray-200 mb-6">{{ config('app.name') }}</h2>
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
                                <a href="{{ route('password.request') }}" class="font-medium text-indigo-600 hover:text-indigo-500"> Mot de passe oublié ? </a>
                            </div>
                        </div>

                        <div>
                            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Se connecter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-front-layout>
