<div>
   <div class="max-w-7xl mx-auto pt-10">
       <x-front.card>
           <x-front.title>
               {{ __('Formulaire de demande de création de compte') }}
           </x-front.title>
           <x-errors class="mb-2"/>
           <form class="space-y-6" wire:submit="send">
               <div class="bg-white px-4 py-5 shadow sm:rounded-lg sm:p-6">
                   <div class="md:grid md:grid-cols-3 md:gap-6">
                       <div class="md:col-span-1">
                           <h3 class="text-lg font-medium leading-6 text-gray-900">{{ __('Informations Professionnelles') }}</h3>
                           <p class="mt-1 text-sm text-gray-500">{{ __('Tous les champs avec * sont obligatoires') }}</p>
                       </div>
                       <div class="mt-5 md:col-span-2 md:mt-0">
                           <div class="grid grid-cols-6 gap-6">
                               <div class="col-span-6 sm:col-span-3">
                                   <x-input label="{{ __('Nom') }} *" wire:model.live="user.nom" id="userNom" />
                               </div>

                               <div class="col-span-6 sm:col-span-3">
                                   <x-input label="{{ __('Prénom') }} *" wire:model.live="user.prenom" id="userPrenom" />
                               </div>

                               <div class="col-span-6 sm:col-span-3">
                                   <x-input type="email" label="{{ __('Email') }} *" wire:model.live="user.email" id="userEmail" />
                               </div>

                               <div class="col-span-6 sm:col-span-3">
                                   <x-input label="{{ __('Téléphone') }} *" wire:model.live="user.telephone" id="userTelephone" />
                               </div>

                               <div class="col-span-6 sm:col-span-3">
                                   <x-input label="{!! __('Nom de l\'Entreprise') !!} *" wire:model.live="entrepriseName"/>
                               </div>

                               <div class="col-span-6 space-y-3">
                                   <x-input label="{{ __('Adresse') }} *" wire:model.live="user.adresse" id="userAdresse" />
                                   <x-input label="{{ __('Complément adresse') }}" wire:model.live="user.adresse_bis" id="userAdresseBis" />
                               </div>

                               <div class="col-span-6 sm:col-span-6 lg:col-span-3">
                                   <x-input label="{{ __('Code postal') }} *" wire:model.live="user.code_postal" id="userCodePostal" />
                               </div>

                               <div class="col-span-6 sm:col-span-3 lg:col-span-3">
                                   <x-input label="{{ __('Ville') }} *" wire:model.live="user.ville" id="userVille" />
                               </div>
                           </div>
                       </div>
                   </div>
               </div>
               <div class="flex justify-end space-x-3">
                   <x-button white label="{{ __('Annuler') }}"  href="{{ route('front.home') }}"/>
                   <x-button type="submit" primary label="{{ __('Envoyer') }}" id="submitButton" />
               </div>
           </form>





           {{--           <form wire:submit="send" class="space-y-2">--}}
{{--               --}}
{{--               --}}
{{--               --}}
{{--               --}}
{{--               --}}
{{--               --}}
{{--               <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">--}}
{{--                   --}}
{{--                   --}}
{{--               </div>--}}
{{--               --}}
{{--               <x-button flat label="{{ __('Annuler') }}" href="{{ route('front.home') }}" />--}}
{{--               <x-button type="submit" label="{{ __('Envoyer') }}" primary wire:loading.attr="disabled"/>--}}
{{--           </form>--}}
       </x-front.card>
   </div>
</div>
