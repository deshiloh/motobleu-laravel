<div>
    @once
        @push('scripts')
            <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.1.1/chart.umd.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-gradient"></script>
        @endpush
    @endonce

    @push('scripts')
        <script>
            const gradientFacturationChart = window['chartjs-plugin-gradient'];
            const myDataFacturation = @JSON($dataset);

            Chart.register(gradientFacturationChart);

            const homeFacturationChart = new Chart(
                document.getElementById('homeChartFacturation'),
                {
                    type: 'line',
                    data : {
                        labels : myDataFacturation.map(row => row.date),
                        datasets: [
                            {
                                gradient: {
                                    backgroundColor: {
                                        axis: 'y',
                                        colors: {
                                            0: 'rgba(217, 119, 6, .1)',
                                            100: 'rgba(217, 119, 6, .2)'
                                        }
                                    },
                                },
                                borderColor : 'rgba(180, 83, 9, 1)',
                                fill: true,
                                data : myDataFacturation.map(row => row.count),
                                tension: 0.2,
                                borderWidth : 4,
                                pointHoverRadius: 10,
                                pointRadius: 0,
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
                            const points = homeFacturationChart.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);

                            if (points.length) {
                                const firstPoint = points[0];
                                const label = homeFacturationChart.data.labels[firstPoint.index];
                                const value = homeFacturationChart.data.datasets[firstPoint.datasetIndex].data[firstPoint.index];
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';

                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            let ttc = context.parsed.y + (context.parsed.y * 0.1);

                                            label += new Intl.NumberFormat('fr-FR', {
                                                style: 'currency',
                                                currency: 'EUR'
                                            }).format(ttc);
                                        }
                                        return label;
                                    }
                                }
                            },
                            gradientFacturationChart,
                            legend : {
                                display: false
                            }
                        }
                    }
                }
            );
        </script>
    @endpush

    <canvas id="homeChartFacturation"></canvas>
</div>
