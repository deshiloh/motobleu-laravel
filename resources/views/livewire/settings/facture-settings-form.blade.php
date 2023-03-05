<div class="pt-6">
    <form class="divide-y divide-gray-200 lg:col-span-9" wire:submit.prevent="save">
        <!-- Profile section -->
        <div class="py-6 px-4 sm:p-6 lg:pb-8">
            <div>
                <h2 class="text-lg font-medium leading-6 text-gray-900">Paramètres facturations</h2>
                <p class="mt-1 text-sm text-gray-500">Paramètres qui concerne la facturation au sein de l'application</p>
            </div>
            <div class="mt-6 grid grid-cols-12 gap-6">
                <div class="col-span-12 sm:col-span-7">
                    <x-select
                        label="Entreprises éligibles aux fichiers XLS"
                        :async-data="route('api.entreprises')"
                        multiselect
                        option-label="nom"
                        option-value="id"
                        wire:model="entreprisesXls"
                        hint="Les entreprises sélectionnées auront un fichier XLS au lieu d'un fichier PDF lors de l'envoi de la facture."
                        placeholder="Sélectionnez une ou plusieurs entreprises"
                    />
                </div>

                <div class="col-span-12 sm:col-span-7">
                    <x-select
                        label="Entreprises éligible au Cost Center et libellé facturations"
                        :async-data="route('api.entreprises')"
                        multiselect
                        option-label="nom"
                        option-value="id"
                        wire:model="entreprisesCostCenterFacturation"
                        hint="Permettra aux entreprises de pouvoir renseigner le Cost Center et la Facturations lors de la création d'une réservation."
                        placeholder="Sélectionnez une ou plusieurs entreprises"
                    />
                </div>
            </div>
        </div>

        <!-- Privacy section -->
        <div class="divide-y divide-gray-200">
            <div class="mt-4 flex justify-end pb-4 px-4 sm:px-6 space-x-2">
                <x-button type="submit" primary label="Sauvegarder" />
            </div>
        </div>
    </form>
</div>
