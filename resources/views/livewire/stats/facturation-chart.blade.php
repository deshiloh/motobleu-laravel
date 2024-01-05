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
                            },
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
                        },
                        responsive: true,
                        scales: {
                            x: {
                                alignToPixels: true,
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    display: false
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
