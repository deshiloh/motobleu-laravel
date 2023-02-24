<div>
   <div class="max-w-7xl mx-auto pt-10">
       <x-front.card>
           <x-front.title>
               {{ __('Formulaire de demande de création de compte') }}
           </x-front.title>
           <x-errors class="mb-2"/>
           <form class="space-y-6" wire:submit.prevent="send">
               <div class="bg-white px-4 py-5 shadow sm:rounded-lg sm:p-6">
                   <div class="md:grid md:grid-cols-3 md:gap-6">
                       <div class="md:col-span-1">
                           <h3 class="text-lg font-medium leading-6 text-gray-900">{{ __('Informations Professionnelles') }}</h3>
                           <p class="mt-1 text-sm text-gray-500">{{ __('Tous les champs avec * sont obligatoires') }}</p>
                       </div>
                       <div class="mt-5 md:col-span-2 md:mt-0">
                           <div class="grid grid-cols-6 gap-6">
                               <div class="col-span-6 sm:col-span-3">
                                   <x-input label="{{ __('Nom') }} *" wire:model.defer="user.nom" />
                               </div>

                               <div class="col-span-6 sm:col-span-3">
                                   <x-input label="{{ __('Prénom') }} *" wire:model.defer="user.prenom" />
                               </div>

                               <div class="col-span-6 sm:col-span-3">
                                   <x-input type="email" label="{{ __('Email') }} *" wire:model.defer="user.email" />
                               </div>

                               <div class="col-span-6 sm:col-span-3">
                                   <x-input label="{{ __('Téléphone') }} *" wire:model.defer="user.telephone" />
                               </div>

                               <div class="col-span-6 sm:col-span-3">
                                   <x-input label="{!! __('Nom de l\'Entreprise') !!} *" wire:model.defer="entrepriseName"/>
                               </div>

                               <div class="col-span-6 space-y-3">
                                   <x-input label="{{ __('Adresse') }} *" wire:model.defer="user.adresse" />
                                   <x-input label="{{ __('Complément adresse') }}" wire:model.defer="user.adresse_bis" />
                               </div>

                               <div class="col-span-6 sm:col-span-6 lg:col-span-3">
                                   <x-input label="{{ __('Code postal') }} *" wire:model.defer="user.code_postal" />
                               </div>

                               <div class="col-span-6 sm:col-span-3 lg:col-span-3">
                                   <x-input label="{{ __('Ville') }} *" wire:model.defer="user.ville" />
                               </div>
                           </div>
                       </div>
                   </div>
               </div>
               <div class="flex justify-end space-x-3">
                   <x-button white label="{{ __('Annuler') }}"  href="{{ route('front.home') }}"/>
                   <x-button type="submit" primary label="{{ __('Envoyer') }}" />
               </div>
           </form>





           {{--           <form wire:submit.prevent="send" class="space-y-2">--}}
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
