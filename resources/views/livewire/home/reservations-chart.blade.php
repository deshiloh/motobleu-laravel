<div wire:key="homeReservationChart">
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
                                    beginAtZero: true,
                                    display: false,
                                    grid: {
                                        display: false
                                    }
                                }
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

                Livewire.on('updateHomeReservationChart', data => {
                    homeReservationChart.data.labels = data.map(row => row.date);
                    homeReservationChart.data.datasets[0].data = data.map(row => row.count);
                    homeReservationChart.update();
                });
            </script>
        @endpush

        <canvas id="homeChartReservation"></canvas>
</div>
