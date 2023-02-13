<x-front-layout>
    <div class="h-[53rem] grid grid-cols-1 md:grid-cols-2 relative">
        <div class="absolute w-full h-full flex items-center justify-center z-10">
            <div class="max-w-4xl px-3 md:px-0">
                <h1>
                    <img src="{{ asset('storage/motobleu-logo.png') }}" alt="Motobleu-Paris">
                </h1>
                <p class="text-center text-4xl text-white py-4">{{ __('Transport de personne à moto') }}</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-10">
                    <div class="block sm:flex sm:justify-end">
                        <a href="{{ route('account.new') }}" class="bg-motobleu block sm:inline-block px-10 py-3 rounded text-center text-white">
                            {{ __('Nouveau client ?') }} <br>
                            <span class="font-bold">{{ __('Enregistrez-vous') }}</span>
                        </a>
                    </div>
                    <div class="block sm:flex justify-start">
                        <a href="{{ route('login') }}" class="bg-white block sm:inline-block px-10 py-3 text-center rounded">
                            {{ __('Déjà client ?') }} <br>
                            <span class="font-bold">{{ __('Connectez-vous') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="relative overflow-hidden" id="slider1"
             x-data="{
                currentIndex: 0,
                nbElement: document.querySelectorAll('#slider1 .sliderItem ').length
             }"
            x-init="
            setInterval(function() {
                currentIndex ++;
                if (currentIndex > nbElement - 1) { currentIndex = 0; }
            }, 9000)
            "
        >
            @foreach(\App\Models\Carousel::where('position', 1)->get() as $photo)
                @php
                    $photoUrl = Storage::disk('photos')->url($photo->file_name);
                @endphp
                <img
                    x-show="currentIndex === {{ $loop->index }}"
                    src="{{ asset($photoUrl) }}"
                    alt=""
                    class="absolute inset-0 h-full w-full object-cover z-0 sliderItem"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform translate-x-full"
                    x-transition:enter-end="opacity-100 transform translate-x-0"
                    x-transition:leave="transition ease-out duration-300"
                    x-transition:leave-start="opacity-100 transform translate-x-0"
                    x-transition:leave-end="opacity-0 transform -translate-x-full"
                >
            @endforeach
        </div>
        <div class="bg-motobleu hidden md:block"></div>
    </div>
    <div class="h-[53rem] grid grid-cols-1 xl:grid-cols-2 relative">
        <div class="bg-motobleu flex items-center justify-center px-8">
            <div class="text-white max-w-4xl">
                <h2 class="text-4xl pb-9">{{ __('Le transport en moto-taxi') }}</h2>
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
                            <div class="font-bold pb-4 text-2xl">{{ __('Nos chauffeurs') }}</div>
                            <div class="text-justify">
                                {{ __("Tous nos chauffeurs sont certifiés par le décret n°2010-1223 du 11 Octobre 2010 applicable au 1er Avril 2011 relatif au transport public de personnes avec conducteur. Nos chauffeurs ont minimum 5 ans de permis moto et 2 ans de pratique en moto-taxi, ils ont tous leur carte professionnelle délivrée par leur préfecture de police, une assurance professionnelle et un autocollant sur le pare-brise avec l'inscription \"transport de personne à  2 ou 3 roues\". Vous pouvez à tout moment leur demander de voir ces documents.") }}
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row items-center md:items-start">
                        <div class="pb-4 md:mr-3 md:pb-0">
                            <div class="rounded-full w-20 h-20 border border-4 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" class="w-2/3 h-2/3">
                                    <image x="0" y="0" width="100%" height="100%" xlink:href="data:img/png;base64,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"></image>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <div class="font-bold pb-4 text-2xl">{{ __('Équipements et sécurité') }}</div>
                            <div class="text-justify">
                                {{ __("Votre confort est une de nos priorités, c'est pourquoi MOTOBLEU met toute une gamme d'équipements à votre disposition pour rendre votre déplacement plus agréable tout en vous protégeant de manière efficace. Casque, pantalon, sanitete « propreté assurée » gants, bluetooth, inter-com, veste équipée de protections (aux coudes, épaules et dos), gilet chauffant, Gilet airbag. Tous nos équipements sont aux normes NF.") }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="relative hidden xl:block overflow-hidden" id="slider2"
             x-data="{
                currentIndex: 0,
                nbElement: document.querySelectorAll('#slider2 .sliderItem ').length
             }"
             x-init="
            setInterval(function() {
                currentIndex ++;
                if (currentIndex > nbElement - 1) { currentIndex = 0; }
            }, 9000)
            "
        >
            @foreach(\App\Models\Carousel::where('position', 2)->get() as $photo)
                @php
                    $photoUrl = Storage::disk('photos')->url($photo->file_name);
                @endphp
                <img
                    x-show="currentIndex === {{ $loop->index }}"
                    src="{{ asset($photoUrl) }}"
                    alt=""
                    class="absolute inset-0 h-full w-full object-cover z-0 sliderItem"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform translate-x-full"
                    x-transition:enter-end="opacity-100 transform translate-x-0"
                    x-transition:leave="transition ease-out duration-300"
                    x-transition:leave-start="opacity-100 transform translate-x-0"
                    x-transition:leave-end="opacity-0 transform -translate-x-full"
                >
            @endforeach
        </div>
    </div>
    <div class="h-[53rem] grid grid-cols-1 xl:grid-cols-2">
        <div class="relative hidden xl:block overflow-hidden" id="slider3"
             x-data="{
                currentIndex: 0,
                nbElement: document.querySelectorAll('#slider3 .sliderItem ').length
             }"
             x-init="
            setInterval(function() {
                currentIndex ++;
                if (currentIndex > nbElement - 1) { currentIndex = 0; }
            }, 9000)
            "
        >
            @foreach(\App\Models\Carousel::where('position', 3)->get() as $photo)
                @php
                    $photoUrl = Storage::disk('photos')->url($photo->file_name);
                @endphp
                <img
                    x-show="currentIndex === {{ $loop->index }}"
                    src="{{ asset($photoUrl) }}"
                    alt=""
                    class="absolute inset-0 h-full w-full object-cover z-0 sliderItem"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform translate-x-full"
                    x-transition:enter-end="opacity-100 transform translate-x-0"
                    x-transition:leave="transition ease-out duration-300"
                    x-transition:leave-start="opacity-100 transform translate-x-0"
                    x-transition:leave-end="opacity-0 transform -translate-x-full"
                >
            @endforeach
        </div>
        <div class="bg-motobleu flex items-center justify-center px-8">
            <div class="text-white max-w-4xl">
                @if(App::currentLocale() == 'fr')
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
                @endif
                @if(App::currentLocale() == 'en')
                        <h3 class="uppercase text-3xl">VIP ACCOUNTS</h3>
                        <p class="mt-3">MOTOBLEU offers to VIP'S all of its resources-experience, service, technology-to increase your companies efficiency, such as:</p>
                        <ul class="list-disc list-inside pl-7 pb-9">
                            <li>Personalized welcome at train stations or airports</li>
                            <li>Monthly Billing</li>
                            <li>All invoices are taken care of</li>
                            <li>Personalized contact</li>
                        </ul>
                        <h4 class="text-2xl mb-3">On Demand Services</h4>
                        <p class="mb-3">
                            The professionalism of MOTOBLEU can meet your specific company needs, for example, having multiple bikes and drivers to take you to any professional obligation.  We are your disposal to take care of all of your projects. <br> Don't hesitate to contact our commercial service enterprise to find the best solution to meet your needs.
                        </p>


                        <p class="mb-3">MOTOBLEU thanks you for your trust.</p>
                        <p>Enjoy your ride with MOTOBLEU Company !</p>
                @endif
            </div>
        </div>
    </div>
    <div class="h-64 bg-white py-5">
        <div class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-10 text-motobleu">
            <div>
                <div class="uppercase text-3xl font-medium mb-6">{{ __('Mentions légales') }}</div>
                <div class="space-y-2">
                    @foreach(\App\Models\Page::all() as $page)
                        <a href="{{ route('pages', ['slug' => $page->slug]) }}" class="block">{{ $page->title }}</a>
                    @endforeach
                </div>
            </div>

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
            Copyright © {{ date('Y') }} {{ __('Tous droits réservés') }}. {{ __('Site créé avec passion par') }}
            <a href="https://agencepoint.com/" class="hover:underline">Agence Point Com</a>
        </p>
    </footer>
    @include('cookie-consent::index')
</x-front-layout>
