<x-front-layout>
    <nav class="bg-motobleu shadow text-white border-b border-black">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 justify-between">
                <div class="flex">
                    <div class="flex flex-shrink-0 items-center">
                        <img class="block h-8 w-auto lg:hidden" src="https://tailwindui.com/img/logos/mark.svg?color=indigo&shade=600" alt="Your Company">
                        <img class="hidden h-8 w-auto lg:block" src="https://tailwindui.com/img/logos/mark.svg?color=indigo&shade=600" alt="Your Company">
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <!-- Current: "border-indigo-500 text-gray-900", Default: "border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700" -->
                        <a href="#" class="inline-flex items-center border-b-2 border-indigo-900 px-1 pt-1 text-sm font-medium text-white">Dashboard</a>
                        <a href="#" class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">Team</a>
                        <a href="#" class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">Projects</a>
                        <a href="#" class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700">Calendar</a>
                    </div>
                </div>
                <div class="hidden sm:ml-6 sm:flex sm:items-center">
                    <div class="flex items-center space-x-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                        </svg>
                        <a href="#">contact@motobleu-paris.com</a>
                    </div>

                </div>
                <div class="-mr-2 flex items-center sm:hidden">
                    <!-- Mobile menu button -->
                    <button type="button" class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500" aria-controls="mobile-menu" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <!--
                          Icon when menu is closed.

                          Heroicon name: outline/bars-3

                          Menu open: "hidden", Menu closed: "block"
                        -->
                        <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                        <!--
                          Icon when menu is open.

                          Heroicon name: outline/x-mark

                          Menu open: "block", Menu closed: "hidden"
                        -->
                        <svg class="hidden h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu, show/hide based on menu state. -->
        <div class="sm:hidden" id="mobile-menu">
            <div class="space-y-1 pt-2 pb-3">
                <!-- Current: "bg-indigo-50 border-indigo-500 text-indigo-700", Default: "border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700" -->
                <a href="#" class="block border-l-4 border-indigo-500 bg-indigo-50 py-2 pl-3 pr-4 text-base font-medium text-indigo-700">Dashboard</a>
                <a href="#" class="block border-l-4 border-transparent py-2 pl-3 pr-4 text-base font-medium text-gray-500 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-700">Team</a>
                <a href="#" class="block border-l-4 border-transparent py-2 pl-3 pr-4 text-base font-medium text-gray-500 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-700">Projects</a>
                <a href="#" class="block border-l-4 border-transparent py-2 pl-3 pr-4 text-base font-medium text-gray-500 hover:border-gray-300 hover:bg-gray-50 hover:text-gray-700">Calendar</a>
            </div>
            <div class="border-t border-gray-200 pt-4 pb-3">
                <div class="flex items-center px-4">
                    <div class="flex-shrink-0">
                        <img class="h-10 w-10 rounded-full" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="">
                    </div>
                    <div class="ml-3">
                        <div class="text-base font-medium text-gray-800">Tom Cook</div>
                        <div class="text-sm font-medium text-gray-500">tom@example.com</div>
                    </div>
                    <button type="button" class="ml-auto flex-shrink-0 rounded-full bg-white p-1 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        <span class="sr-only">View notifications</span>
                        <!-- Heroicon name: outline/bell -->
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                        </svg>
                    </button>
                </div>
                <div class="mt-3 space-y-1">
                    <a href="#" class="block px-4 py-2 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800">Your Profile</a>
                    <a href="#" class="block px-4 py-2 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800">Settings</a>
                    <a href="#" class="block px-4 py-2 text-base font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-800">Sign out</a>
                </div>
            </div>
        </div>
    </nav>
    <div class="h-[53rem] grid grid-cols-1 md:grid-cols-2 relative">
        <div class="absolute w-full h-full flex items-center justify-center z-10">
            <div class="max-w-4xl px-3 md:px-0">
                <h1>
                    <img src="{{ asset('storage/motobleu-logo.png') }}" alt="Motobleu-Paris">
                </h1>
                <p class="text-center text-4xl text-white py-4">Transport de personne à moto</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-10">
                    <div class="block sm:flex sm:justify-end">
                        <a href="#" class="bg-motobleu block sm:inline-block px-10 py-3 rounded text-center text-white">
                            Nouveau client ? <br>
                            <span class="font-bold">Enregistrez-vous</span>
                        </a>
                    </div>
                    <div class="block sm:flex justify-start">
                        <a href="#" class="bg-white block sm:inline-block px-10 py-3 text-center rounded">
                            Déjà client ? <br>
                            <span class="font-bold">Connectez-vous</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="relative" id="slider1"
             x-data="{
                currentIndex: 0,
                nbElement: document.querySelectorAll('#slider1 .sliderItem ').length
             }"
            x-init="
            setInterval(function() {
                currentIndex ++;
                if (currentIndex > nbElement - 1) { currentIndex = 0; }
            }, 5000)
            "
        >
            @for($i = 0; $i < 3; $i ++)
                <img
                    x-show="currentIndex == {{ $i }}"
                    src="https://picsum.photos/id/1{{ $i }}/2000/2000"
                    alt=""
                    class="absolute inset-0 h-full w-full object-cover z-0 sliderItem"
                    x-transition
                >
            @endfor
        </div>
        <div class="bg-motobleu hidden md:block"></div>
    </div>
    <div class="h-[53rem] grid grid-cols-1 xl:grid-cols-2 relative">
        <div class="bg-motobleu flex items-center justify-center px-8">
            <div class="text-white max-w-4xl">
                <h2 class="text-4xl pb-9">Le transport en moto-taxi</h2>
                <div class="space-y-9">
                    <div class="flex flex-col md:flex-row items-center md:items-start">
                        <div class="pb-4 md:mr-3 md:pb-0">
                            <div class="rounded-full w-20 h-20 border border-4 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-2/3 h-2/3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <div class="font-bold pb-4 text-2xl">Nos chauffeurs</div>
                            <div class="text-justify">
                                Tous nos chauffeurs sont certifiés par le décret n°2010-1223 du 11 Octobre 2010
                                applicable au 1er Avril 2011 relatif au transport public de personnes avec conducteur.
                                Nos chauffeurs ont minimum 5 ans de permis moto et 2 ans de pratique en moto-taxi,
                                ils ont tous leur carte professionnelle délivrée par leur préfecture de police, une
                                assurance professionnelle et un autocollant sur le pare-brise avec l'inscription
                                "transport de personne à  2 ou 3 roues". Vous pouvez à tout moment leur demander de
                                voir ces documents.
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row items-center md:items-start">
                        <div class="pb-4 md:mr-3 md:pb-0">
                            <div class="rounded-full w-20 h-20 border border-4 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" class="w-2/3 h-2/3">
                                    <image x="0" y="0" width="100%" height="100%" xlink:href="data:img/png;base64,iVBORw0KGgoAAAANSUhEUgAAAFcAAABlCAQAAACSc7dBAAAABGdBTUEAALGPC/xhBQAAACBjSFJNAAB6JgAAgIQAAPoAAACA6AAAdTAAAOpgAAA6mAAAF3CculE8AAAAAmJLR0QA/4ePzL8AAAAHdElNRQfiDA0UNywZMuKOAAAGXElEQVR42tWca2wUVRTHf7tdWh6FlvIokZZWmvJqQUTkLahIgxQqkvoBCKAYITEiCIaghKSGEI2aaPiAj0QkMQQ0EkBAQRAqLwsiJUgQpEUe2oqForQUaLs9ftjZ3W67u92dmTuz/O+HuTPbe+e3Z8/ce+5j6hBMUB9GkEsGGaTThS60B6CJKqqo5ALn+ZWfqTV+I4ch3HQKmMIIukfwt25KOcA2SmgycEfRl3rIcikVPaqQD2SQzrvqwh0rG+WeLlS/imWyONTjjpQ9BkH9KpVpKnGzZZtpqF7tkYEqcBNktWEHCK5GKZJ25uIOkTNKUL06JYPNw31Z7iqFFRGpk7lm4CbIBuWoXn0kLmO43eSQZbAiIrulc3jccL1aGrvJMd5xRqVT5FEV+uPQuGkUk2UxLMAZJvNXtLipHLEFFqCc8VQE/8gZ9GoXvrMNFrLYQ1LkuHFs4mHbYAFy2UXHSHHXMMVWWICxbMAR5HqrxmKapU1XOBW13ZD14GxEwbY1msHWwAstnWFdDMHCejLC4T5Hod2EAUpmE65QuJ1YazdfK43m1VC4r9PLbrogWk2m/8T/qPWijE52swXVVmZ4s37rroxRWHiWMd6s17opXA3ej8SEDjLBk/Fad2EMw8J4r3091m3HJR6wmymstjMdvNadFOOwUEAfP+5Mu2nalIPnweMMHaiK2VbBr4tkeaybdx/AQl+GenCfsJskQk334D5uN0eEmgoOSeF60Lg99uQm2cmw+wQW4hjnZJDdFFHoUSf97WaIQoOdDLCbIQoNcZFqsIrr7OcUN6hXCtqZLKbRF/nTwND6isyS+GjXF3SnOHnNIbd1h447mE2NUpu2kkP0rgPuZipua2FDTem1rRvM0WAH8gnnqVXqB3e4yioAdHrtMq2mQgvWLTxyS3vR6QyNpFINZPAbHSzzhN5UuKjT8aj9RDUAiwJgSzlJHCOU9ZPx4KJGB26Zdpzgu3KeeRzT8pP4nN5qiJ26mqJb2tELdYHRPljYyxj+UYV7zUBp7++ygJsB16+wTBVuheE6yikGoB9b+JJ0AL5S04G4qDRQ+g5JwEntbA0zgFu8BNRzlpHm4zq5ZKB0B60OjxoAaNTOlAT9Lt9Trl/DtOMbuHFTBEACuWpwyw3X8SB5fA9cZo7v2mw1c25Oftd+RD3yxriftoia+/G+Clhw0sjZqEv11I5ntGMGx8nXvDWOWRylqxpcF3Cah6Is5V2H3+ybUunDTio5jYNhSteOBHlFR3yUJgjikiMWxWMiIpmCEziu41suBqCRZzio0JZBrRuvI2a9J7m+EdQSuWWldespifpbxrNDW1F08yH92WyNcT09UrGOkpmUMFHLVzKTiZyzgFcQZILuH+gz6e4bUsXLCqlT6wyeG7Uz4H3XZX6zzZcZslU9LgZ3Nx6SnGYD13wpV4Xrjaa+NeRR4zjF274oYRc5FHFPne8i3aTB8Le/JPnNbJwlu8y3rr/67aZUuUXSmyEXyB+qcJ80qdI6WSkJvlo7yhrTNtKmBu6BNG+/Y1mAW2Sb4hY1Ehe4dWg4x3TPmbXWTpY0C/2H8jTZOIHEwO0qET5gN9jIAVpszHrPNPuKiNyV1dLR3Lm9wNN4OWoqsMhlKVSHi6RKmcnAIiUyU5LMwW29wzSN/WSb3rw3UcbVgNdoGrgdRfn1nrg62IbYFL6OuXXiieyH4LPn1eRRZP1EflhpwWno3dGP8DHD7abUdJMUTyZ0O/sLo1hs9cpOSBbawgU3axnAFrtZgROR4AJUUEg+F2zG/dGbiez1uniWsIpEm2DdJHubwMhihHrepR9f2IR7wt9eRx7SVDKXURy2AbfYn40uAjvGY0znvH24enpulyyUv02PLEKpQRJDhziRpkRZIdctwT3c/L56w/Fa3iGTN/lPuSv8EHBmMKRLliKpVmrdceEDyOiVyAKWKlpWrSHFt5ZkgnX945D5ck6Bbb8JvI9ZQ8l61jOIAvaabN19Lc7NHEkJggyUdVJrmnVbvEVshu+2VhIv8KIJC4EXW701Z7p1/WmEfGJwGeCtlnWqxEWQTjJP9olbF+y/0sNqXE/qKYvkcNS4c1rXZA2uJ/WRpVIsjRGhNsnyYHVYietJXWWWbJALYWHPyFPBS6tpGSJRL8YylBwySKMr7QDhGhc5znYOhvqHHv8DGqZh3W1ZAEEAAAAASUVORK5CYII="></image>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <div class="font-bold pb-4 text-2xl">Équipements et sécurité</div>
                            <div class="text-justify">
                                Votre confort est une de nos priorités, c'est pourquoi MOTOBLEU met toute une gamme
                                d'équipements à votre disposition pour rendre votre déplacement plus agréable tout en
                                vous protégeant de manière efficace. Casque, pantalon, sanitete « propreté assurée » gants,
                                bluetooth, inter-com, veste équipée de protections (aux coudes, épaules et dos), gilet
                                chauffant, Gilet airbag. Tous nos équipements sont aux normes NF.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="relative hidden xl:block" id="slider2"
             x-data="{
                currentIndex: 0,
                nbElement: document.querySelectorAll('#slider2 .sliderItem ').length
             }"
             x-init="
            setInterval(function() {
                currentIndex ++;
                if (currentIndex > nbElement - 1) { currentIndex = 0; }
            }, 5000)
            "
        >
            @for($i = 0; $i < 3; $i ++)
                <img
                    x-show="currentIndex == {{ $i }}"
                    src="https://picsum.photos/id/1{{ $i }}/2000/2000"
                    alt=""
                    class="absolute inset-0 h-full w-full object-cover z-0 sliderItem"
                    x-transition
                >
            @endfor
        </div>
    </div>
    <div class="h-[53rem] grid grid-cols-1 xl:grid-cols-2">
        <div class="relative hidden xl:block" id="slider3"
             x-data="{
                currentIndex: 0,
                nbElement: document.querySelectorAll('#slider3 .sliderItem ').length
             }"
             x-init="
            setInterval(function() {
                currentIndex ++;
                if (currentIndex > nbElement - 1) { currentIndex = 0; }
            }, 5000)
            "
        >
            @for($i = 0; $i < 3; $i ++)
                <img
                    x-show="currentIndex == {{ $i }}"
                    src="https://picsum.photos/id/1{{ $i }}/2000/2000"
                    alt=""
                    class="absolute inset-0 h-full w-full object-cover z-0 sliderItem"
                    x-transition
                >
            @endfor
        </div>
        <div class="bg-motobleu flex items-center justify-center px-8">
            <div class="text-white max-w-4xl">
                <h3 class="uppercase text-3xl">Solutions entreprises</h3>
                <p class="mb-3">Le transport de personnes à moto-taxi est une offre de service en pleine émergence destinée aux entreprises.</p>

                <div class="text-2xl mb-3">Offres grands-comptes</div>
                MOTOBLEU met à la disposition des grands comptes, la palette de ses ressources – expérience, services, technologies – pour leur permettre d'accroitre leur efficacité, et leur propose un ensemble de prestations adapté à leur contexte :

                <ul class="list-disc list-inside pl-7 pb-9">
                    <li>Accueil personnalisé en gare et aéroport.</li>
                    <li>Facturation mensuelle</li>
                    <li>Gestion de compte et facturation par sous-compte.</li>
                    <li>Contact dédié et personnalisé.</li>
                    <li>Service à la demande</li>
                </ul>
                <p class="mb-3">
                    Le savoir-faire et la capacité du service MOTOBLEU permet à l'entreprise de répondre sans délai à
                    des demandes plus spécifiques, incluant la mise à disposition de plusieurs motos et de leurs pilotes
                    pour assurer la couverture d'une manifestation ponctuelle ou régulière (évènementiel, salon
                    professionnel, séminaire.....). <br>
                    Nous sommes à votre disposition pour étudier tous vos projets. <br>
                    N'hésitez pas à contacter notre service commercial « entreprise » afin d'étudier la solution la mieux adaptée.
                </p>
                <p>MOTOBLEU vous remercie de votre confiance !</p>
            </div>
        </div>
    </div>
    <div class="h-64 bg-white py-5">
        <div class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-10 text-motobleu">
            <div class="uppercase text-3xl font-medium">Mentions légales</div>
            <div>
                <div class="text-3xl pb-6 uppercase font-medium">Informations</div>
                <p>
                    Motobleu <br>
                    26-28 rue Marius Aufan <br>
                    92300 Levallois Perret <br>
                    Tél: +33647938617 <br>
                    contact@motobleu-paris.com <br>
                    RCS 824 721 955 Nanterre
                </p>
            </div>
        </div>
    </div>
    <footer class="py-3">
        <p class="text-center text-white">
            Copyright © 2019 Tous droits réservés. Site créé avec passion par
            <a href="https://agencepoint.com/" class="hover:underline">Agence Point Com</a>
        </p>
    </footer>
</x-front-layout>
