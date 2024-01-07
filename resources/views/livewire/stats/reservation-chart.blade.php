<div>
    @once
        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        @endpush
    @endonce

    @push('scripts')
        <script>
            const chart = new Chart(
                document.getElementById('chart'), {
                    type: 'line',
                    data: {
                        labels: @json($labels),
                        datasets: @json($dataset)
                    },
                    options : {
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        responsive: true,
                        scales: {
                            x: {
                                alignToPixels: true,
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 16 // taille de la police pour l'axe X
                                    }
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 16 // taille de la police pour l'axe X
                                    }
                                }
                            }
                        }
                    }
                }
            );

            Livewire.on('updateChart', data => {
                chart.data = data;
                chart.update();
            });
        </script>
    @endpush

    <canvas id="chart"></canvas>
</div>
