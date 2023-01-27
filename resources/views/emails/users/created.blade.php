@component('mail::message')
# Compté créé

Bonjour ! <br><br>
Veuillez trouver votre identifiant : {{ $account->email }} <br>
Pour obtenir votre mot de passe, merci de cliquer sur le lien suivant : <br>

@component('mail::button', ['url' => route('password.reset', ['token' => $token, 'email' => $account->email])])
Changer mon mot de passe
@endcomponent

Merci,<br>
{{ config('app.name') }}
@endcomponent
