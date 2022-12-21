<div wire:poll="reloadData">
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

                const myChart = new Chart(
                    document.getElementById('chart').getContext("2d"),
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
                                    borderWidth : 5,
                                    pointHoverRadius: 10
                                }
                            ]
                        },
                        options: {
                            scales: {
                                x: {
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
                            hitRadius: 30,
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
                    myChart.data.labels = data.map(row => row.date);
                    myChart.data.datasets[0].data = data.map(row => row.count);
                    myChart.update();
                });
            </script>
        @endpush
        <canvas id="chart" class="mt-4"></canvas>
</div>
