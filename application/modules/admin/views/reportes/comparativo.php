<div class="reporte-contenedor" style="background:#fff;padding:20px;border:1px solid #ddd;">
    <h2>Comparativo de sucursales</h2>
    <canvas id="comparativoChart"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('comparativoChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($etiquetas) ?>,
            datasets: [
                <?php foreach ($datasets as $ds): ?>
                {
                    label: '<?= $ds['label'] ?>',
                    data: <?= json_encode($ds['data']) ?>,
                    fill: false,
                    tension: 0.1
                },
                <?php endforeach; ?>
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Comparativo de ventas por sucursal'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>