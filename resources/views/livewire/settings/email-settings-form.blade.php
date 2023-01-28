<div class="pt-6">
    <form class="divide-y divide-gray-200 lg:col-span-9" wire:submit.prevent="save">
        <!-- Privacy section -->
        <div class="divide-y divide-gray-200 pt-6">
            <div class="px-4 sm:px-6">
                <div>
                    <h2 class="text-lg font-medium leading-6 text-gray-900">Tests Emails</h2>
                    <p class="mt-1 text-sm text-gray-500">Permet d'envoyer des emails de tests à l'adresse renseignée plus bas. Les données utilisées seront votre compte et la première réservation qui existe dans l'application</p>
                </div>
                <ul role="list" class="mt-2 divide-y divide-gray-200">
                    <li class="py-4 grid grid-cols-6">
                        <div class="col-span-6 sm:col-span-3 space-y-3">
                            <x-input type="email" label="Adresse email pour les tests" wire:model="emailTest"/>
                            <x-toggle label="Mode administrateur (Tous les mails ne sont pas concernés)" wire:model="adminMode" md/>
                        </div>
                    </li>
                    <li class="flex items-center justify-between py-4">
                        <div class="flex flex-col">
                            <p class="text-sm font-medium text-gray-900" id="privacy-option-1-label">Demande de création de compte</p>
                            <p class="text-sm text-gray-500" id="privacy-option-2-description">Email envoyé à <span class="font-bold">l'administrateur</span> après remplissage du formulaire de demande de création de compte</p>
                        </div>
                        <x-button label="Envoyer le test" type="button" wire:click="sendEmailTest('{!! \App\Mail\RegisterUserDemand::class !!}')"/>
                    </li>
                    <li class="flex items-center justify-between py-4">
                        <div class="flex flex-col">
                            <p class="text-sm font-medium text-gray-900" id="privacy-option-3-label">Compte créé</p>
                            <p class="text-sm text-gray-500" id="privacy-option-3-description">Email envoyé lorsque le compte a bien été créé.</p>
                        </div>
                        <x-button label="Envoyer le test" wire:click="sendEmailTest('{{ \App\Mail\UserCreated::class }}')"/>
                    </li>
                    <li class="flex items-center justify-between py-4">
                        <div class="flex flex-col">
                            <p class="text-sm font-medium text-gray-900" id="privacy-option-2-label">Confirmation d'envoi de la demande de création de compte</p>
                            <p class="text-sm text-gray-500" id="privacy-option-2-description">Email de confirmation envoyé à la personne ayant faite la demande</p>
                        </div>
                        <x-button label="Envoyer le test" wire:click="sendEmailTest('{{ \App\Mail\ConfirmationRegisterUserDemand::class }}')" />
                    </li>
                    <li class="flex items-center justify-between py-4">
                        <div class="flex flex-col">
                            <p class="text-sm font-medium text-gray-900" id="privacy-option-3-label">Réservation Créée</p>
                            <p class="text-sm text-gray-500 mb-3" id="privacy-option-3-description">Emails envoyés lors de la création d'une réservation</p>
                        </div>
                        <x-button label="Envoyer le test" wire:click="sendEmailTest('{{ \App\Mail\ReservationCreated::class }}')"/>
                    </li>
                    <li class="flex items-center justify-between py-4">
                        <div class="flex flex-col">
                            <p class="text-sm font-medium text-gray-900" id="privacy-option-3-label">Réservation Modifiée</p>
                            <p class="text-sm text-gray-500 mb-3" id="privacy-option-3-description">Emails envoyés lors de la modification d'une réservation</p>
                        </div>
                        <x-button label="Envoyer le test" wire:click="sendEmailTest('{{ \App\Mail\ReservationUpdated::class }}')"/>
                    </li>
                    <li class="flex items-center justify-between py-4">
                        <div class="flex flex-col">
                            <p class="text-sm font-medium text-gray-900" id="privacy-option-3-label">Réservation Confirmée</p>
                            <p class="text-sm text-gray-500" id="privacy-option-3-description">Emails envoyés lorsque la réservation a été confirmée</p>
                        </div>
                        <x-button label="Envoyer le test" wire:click="sendEmailTest('{{ \App\Mail\ReservationConfirmed::class }}')"/>
                    </li>
                    <li class="flex items-center justify-between py-4">
                        <div class="flex flex-col">
                            <p class="text-sm font-medium text-gray-900" id="privacy-option-3-label">Réservation Annulée</p>
                            <p class="text-sm text-gray-500" id="privacy-option-3-description">Emails envoyés lorsque la réservation a été annulée</p>
                        </div>
                        <x-button label="Envoyer le test" wire:click="sendEmailTest('{{ \App\Mail\ReservationCanceled::class }}')"/>
                    </li>
                    <li class="flex items-center justify-between py-4">
                        <div class="flex flex-col">
                            <p class="text-sm font-medium text-gray-900" id="privacy-option-3-label">Demande d'annulation d'une réservation</p>
                            <p class="text-sm text-gray-500" id="privacy-option-3-description">Email envoyé lorsque une assistante demande l'annulation d'une réservation</p>
                        </div>
                        <x-button label="Envoyer le test" wire:click="sendEmailTest('{{ \App\Mail\CancelReservationDemand::class }}')"/>
                    </li>
                    <li class="flex items-center justify-between py-4">
                        <div class="flex flex-col">
                            <p class="text-sm font-medium text-gray-900" id="privacy-option-3-label">Demande de modification d'une réservation</p>
                            <p class="text-sm text-gray-500" id="privacy-option-3-description">Email envoyé lorsque une assistante demande une modification d'une réservation</p>
                        </div>
                        <x-button label="Envoyer le test" wire:click="sendEmailTest('{{ \App\Mail\UpdateReservationDemand::class }}')"/>
                    </li>
                    <li class="flex items-center justify-between py-4">
                        <div class="flex flex-col">
                            <p class="text-sm font-medium text-gray-900" id="privacy-option-3-label">Pilote ajouté à une réservation</p>
                            <p class="text-sm text-gray-500" id="privacy-option-3-description">Email envoyé lorsque un pilote est ajouté dans une réservation</p>
                        </div>
                        <x-button label="Envoyer le test" wire:click="sendEmailTest('{{ \App\Mail\PiloteAttached::class }}')"/>
                    </li>
                    <li class="flex items-center justify-between py-4">
                        <div class="flex flex-col">
                            <p class="text-sm font-medium text-gray-900" id="privacy-option-3-label">Pilote retiré d'une réservation</p>
                            <p class="text-sm text-gray-500" id="privacy-option-3-description">Email envoyé lorsque pilote est retiré d'une réservation (modification du pilote ou annulation de la réservation)</p>
                        </div>
                        <x-button label="Envoyer le test" wire:click="sendEmailTest('{{ \App\Mail\PiloteDetached::class }}')"/>
                    </li>
                </ul>
            </div>
{{--            <div class="mt-4 flex justify-end py-4 px-4 sm:px-6 space-x-2">--}}
{{--                <x-button type="submit" primary label="Sauvegarder" />--}}
{{--            </div>--}}
        </div>
    </form>
</div>
