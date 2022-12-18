<div wire:poll="reloadData">
    @once
        @push('scripts')
            <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.1.1/chart.umd.js"></script>
        @endpush
    @endonce

        @push('scripts')
            <script>
                const myData = @JSON($dataset);

                const myChart = new Chart(
                    document.getElementById('chart'),
                    {
                        type: 'bar',
                        data : {
                            labels : myData.map(row => row.date),
                            datasets: [
                                {
                                    data : myData.map(row => row.count)
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            //maintainAspectRatio: false,
                            onClick: (evt) => {
                                const points = myChart.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);

                                if (points.length) {
                                    const firstPoint = points[0];
                                    const label = myChart.data.labels[firstPoint.index];
                                    const value = myChart.data.datasets[firstPoint.datasetIndex].data[firstPoint.index];

                                    console.log(label, value);
                                }
                            },
                            scales : {
                                y : {
                                    title : {
                                        text : "Nombre de réservations",
                                        display: true
                                    }
                                },
                                x: {
                                    title : {
                                        text : "Période",
                                        display: true
                                    }
                                }
                            },
                            plugins: {
                                legend : {
                                    display: false
                                }
                            }
                        }
                    }
                );

                Livewire.on('updateChart', data => {
                    myChart.data.labels = data.map(row => row.date);
                    myChart.data.datasets[0].data = data.map(row => row.count);
                    myChart.update();
                });
            </script>
        @endpush
        <h3 class="dark:text-white text-xl">Stats</h3>
        <canvas id="chart" class="mt-4"></canvas>
        <div class="dark:text-white text-xs">Entreprise avec le plus de réservations : REFEEFEF</div>
        <div class="dark:text-white text-xs">Entreprise avec le moins de réservation : REFEEFEF</div>
</div>
