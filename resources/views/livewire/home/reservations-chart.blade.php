<div wire:poll="reloadData" wire:key="chart">
    @once
        @push('scripts')
            <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.1.1/chart.umd.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-gradient"></script>
        @endpush
    @endonce

        @push('scripts')
            <script>
                const gradient = window['chartjs-plugin-gradient'];
                const myData = @JSON($dataset);
                var delayed;

                Chart.register(gradient);

                const homeReservationChart = new Chart(
                    document.getElementById('homeChartReservation'),
                    {
                        type: 'line',
                        data : {
                            labels : myData.map(row => row.date),
                            datasets: [
                                {
                                    gradient: {
                                        backgroundColor: {
                                            axis: 'y',
                                            colors: {
                                                0: 'rgba(96, 165, 250, .1)',
                                                100: 'rgba(96, 165, 250, .2)'
                                            }
                                        },
                                    },
                                    borderColor : 'rgba(37, 99, 235, 1)',
                                    fill: true,
                                    data : myData.map(row => row.count),
                                    tension: 0.2,
                                    borderWidth : 4,
                                    pointHoverRadius: 10,
                                    pointRadius: 0
                                }
                            ]
                        },
                        options: {
                            scales: {
                                x: {
                                    alignToPixels: true,
                                    grid: {
                                        display: false
                                    }
                                },
                                y: {
                                    display: false,
                                    grid: {
                                        display: false
                                    }
                                }
                            },
                            animation: {
                                onComplete: () => {
                                    delayed = true;
                                },
                                delay: (context) => {
                                    let delay = 0;
                                    if (context.type === 'data' && context.mode === 'default' && !delayed) {
                                        delay = context.dataIndex * 300 + context.datasetIndex * 100;
                                    }
                                    return delay;
                                },
                            },
                            hitRadius: 40,
                            responsive: true,
                            onClick: (evt) => {
                                const points = myChart.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);

                                if (points.length) {
                                    const firstPoint = points[0];
                                    const label = myChart.data.labels[firstPoint.index];
                                    const value = myChart.data.datasets[firstPoint.datasetIndex].data[firstPoint.index];
                                }
                            },
                            plugins: {
                                gradient,
                                legend : {
                                    display: false
                                }
                            }
                        }
                    }
                );

                Livewire.on('updateChart', data => {
                    homeReservationChart.data.labels = data.map(row => row.date);
                    homeReservationChart.data.datasets[0].data = data.map(row => row.count);
                    homeReservationChart.update();
                });
            </script>
        @endpush

    <div class="relative bg-white dark:bg-slate-800 pt-5 px-4 pb-12 sm:pt-6 sm:px-6 shadow rounded-lg overflow-hidden">
        <div class="absolute">
            <div class="flex">
                <div class="flex flex-col ml-4">
                    <p class="text-xl font-medium text-gray-500 truncate">Réservations</p>
                    <div class="text-6xl font-semibold text-gray-900 dark:text-gray-200" >
                        {{ $nbTotalReservation }}
                    </div>
                </div>
            </div>
        </div>
        <dd class="pb-6 flex items-baseline sm:pb-7">
            <div class="absolute bottom-0 inset-x-0 bg-gray-50 dark:bg-slate-700 px-4 py-4 sm:px-6">
                <div class="grid grid-cols-2">
                    <div>
                        <div class="inline-flex items-baseline px-2.5 py-0.5 rounded-lg text-sm font-medium bg-green-100 text-green-800 md:mt-2 lg:mt-0">

                            <svg class="-ml-1 mr-0.5 h-5 w-5 flex-shrink-0 self-center text-green-500" x-description="Heroicon name: mini/arrow-up" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 17a.75.75 0 01-.75-.75V5.612L5.29 9.77a.75.75 0 01-1.08-1.04l5.25-5.5a.75.75 0 011.08 0l5.25 5.5a.75.75 0 11-1.08 1.04l-3.96-4.158V16.25A.75.75 0 0110 17z" clip-rule="evenodd"></path>
                            </svg>

                            <a href="{!! route('admin.entreprises.show', ['entreprise' => $firstEntreprise->id]) !!}">{{ $firstEntreprise->nom }}</a>

                        </div>
                    </div>
                    <div class="flex justify-end">
                        <div class="inline-flex items-baseline px-2.5 py-0.5 rounded-lg text-sm font-medium bg-red-100 text-red-800 md:mt-2 lg:mt-0">

                            <svg class="-ml-1 mr-0.5 h-5 w-5 flex-shrink-0 self-center text-red-500" x-description="Heroicon name: mini/arrow-down" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 3a.75.75 0 01.75.75v10.638l3.96-4.158a.75.75 0 111.08 1.04l-5.25 5.5a.75.75 0 01-1.08 0l-5.25-5.5a.75.75 0 111.08-1.04l3.96 4.158V3.75A.75.75 0 0110 3z" clip-rule="evenodd"></path>
                            </svg>

                            <a href="{!! route('admin.entreprises.show', ['entreprise' => $lastEntreprise->id]) !!}">{{ $lastEntreprise->nom }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </dd>
        <div class="mb-6">
            <canvas id="homeChartReservation"></canvas>
        </div>
    </div>
</div>
