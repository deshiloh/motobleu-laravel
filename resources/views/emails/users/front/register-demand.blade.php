<x-mail::message>
# Demande de création de compte
Une demande de création de compte vient d'être envoyé :


<x-mail::table>
    |               |                                 |
    |---------------|---------------------------------|
    |           Nom |             {{ $datas['nom'] }} |
    |        Prenom |          {{ $datas['prenom'] }} |
    | Adresse email |           {{ $datas['email'] }} |
    |     Téléphone |       {{ $datas['telephone'] }} |
    |       Adresse |         {{ $datas['adresse'] }} |
    |   Adresse Bis |     {{ $datas['adresse_bis'] }} |
    |   Code postal |     {{ $datas['code_postal'] }} |
    |         Ville |           {{ $datas['ville'] }} |
    |    Entreprise | {{ $datas['entreprise_name'] }} |
</x-mail::table>

</x-mail::message>
