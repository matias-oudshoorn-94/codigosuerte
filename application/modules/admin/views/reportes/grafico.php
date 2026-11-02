<div class="reporte-contenedor" style="background:#fff;padding:20px;border:1px solid #ddd;">
    <h2>Gráfico de ventas</h2>
    <canvas id="ventasChart"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('ventasChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($etiquetas) ?>,
            datasets: [{
                label: 'Total Ventas',
                data: <?= json_encode($valores) ?>,
                borderColor: 'rgb(75, 192, 192)',
                fill: false,
                tension: 0.1
            }]
        }
    });
</script>