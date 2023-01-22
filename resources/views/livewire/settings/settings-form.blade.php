<div>
    <x-header>
        Paramètres de l'application
    </x-header>

    <main class="relative ">
        <div class="container mx-auto px-4 pb-6 sm:px-6 lg:px-8">
            <div class="rounded-lg bg-white shadow">
                <div class="divide-y divide-gray-200 lg:grid lg:grid-cols-12 lg:divide-y-0 lg:divide-x" x-data="tabs()">
                    <aside class="py-6 lg:col-span-3">
                        <nav class="space-y-1">
                            <template x-for="tab in tabs" :key="tab.id">
                                <a
                                    @click="activeTab = tab.id"
                                    href="#"
                                    class="group border-l-4 px-3 py-2 flex items-center text-sm font-medium"
                                    x-bind:class="{
                                        'bg-teal-50 border-teal-500 text-teal-700 hover:bg-teal-50 hover:text-teal-700' : isActiveTab(tab.id),
                                        'border-transparent text-gray-900 hover:bg-gray-50 hover:text-gray-900' : ! isActiveTab(tab.id)
                                    }"
                                >
                                    <!-- Heroicon name: outline/cog -->
                                    <div
                                        x-html="tab.icon"
                                        class="flex-shrink-0 -ml-1 mr-3"
                                        x-bind:class="{
                                            'text-teal-500 group-hover:text-teal-500' : isActiveTab(tab.id),
                                            'text-gray-400 group-hover:text-gray-500' : ! isActiveTab(tab.id)
                                        }"
                                    ></div>
                                    <span class="truncate" x-text="tab.title"></span>
                                </a>
                            </template>
                        </nav>
                    </aside>

                    <div class="lg:col-span-9 divide-y divide-gray-200">
                        <div x-show="activeTab === 0">
                            <livewire:settings.email-settings-form />
                        </div>
                        <div x-show="activeTab === 1">
                            <livewire:settings.facture-settings-form />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script>
        function tabs() {
            return {
                activeTab: 0,
                tabs : [
                    {
                        id: 0,
                        title: 'Emails',
                        icon: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                </svg>`
                    },
                    {
                        id: 1,
                        title: 'Facturations',
                        icon: `<svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                                </svg>`
                    }
                ],
                isActiveTab(id) {
                    return id === this.activeTab
                }
            }
        }
    </script>
</div>
