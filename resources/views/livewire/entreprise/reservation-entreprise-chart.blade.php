<div>
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
                                borderWidth : 4,
                                pointHoverRadius: 10,
                                pointRadius: 0,
                            }
                        ]
                    },
                    options: {
                        hitRadius: 30,
                        responsive: true,
                        scales : {
                            y : {
                                display: false,
                                grid : {
                                    display: false
                                },
                                beginAtZero: true,
                            },
                            x: {
                                grid : {
                                    display: false
                                },
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
    <canvas id="chart"></canvas>
</div>
