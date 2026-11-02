<div class="reporte-contenedor" style="background:#fff;padding:20px;border:1px solid #ddd;">

    <?php if (!empty($reporte) && $reporte['total_vendido'] > 0) : ?>
        <table border="1" cellpadding="6" cellspacing="0">
            <tr>
                <th>Total Vendido</th>
                <th>Efectivo</th>
                <th>Tarjeta</th>
                <th>Medio Electrónico</th>
                <th>Transferencia</th>
            </tr>
            <tr>
                <td>$<?= number_format($reporte['total_vendido'], 2, ',', '.') ?></td>
                <td>$<?= number_format($reporte['efecto'], 2, ',', '.') ?></td>
                <td>$<?= number_format($reporte['tarjeta'], 2, ',', '.') ?></td>
                <td>$<?= number_format($reporte['medio_electronico'], 2, ',', '.') ?></td>
                <td>$<?= number_format($reporte['transferencia'], 2, ',', '.') ?></td>
            </tr>
        </table>
    <?php else: ?>
        <p>No se encontraron datos para el mes seleccionado.</p>
    <?php endif; ?>
</div>