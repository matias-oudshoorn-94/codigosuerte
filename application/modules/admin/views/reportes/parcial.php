<div class="reporte-contenedor" style="background:#fff;padding:20px;border:1px solid #ddd;">
    <h2>Reporte parcial</h2>
    <p><strong>Sucursal:</strong> <?php echo $id_usuario; ?></p>
    <p><strong>Desde:</strong> <?php echo $fecha_desde; ?> | <strong>Hasta:</strong> <?php echo $fecha_hasta; ?></p>

    <?php if (!empty($reporte)) : ?>
        <table border="1" cellpadding="6" cellspacing="0" style="width:100%;border-collapse:collapse;margin-top:20px;">
            <thead style="background:#eee;">
                <tr>
                    <th>Fecha</th>
                    <th>Ventas</th>
                    <th>Cantidad</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reporte as $fila): ?>
    <tr>
        <td><?= $fila['fecha']; ?></td>
        <td>$<?= number_format($fila['total'], 2, ',', '.'); ?></td>
        <td><?= $fila['cantidad']; ?></td>
    </tr>
<?php endforeach; ?>
            </tbody>
            <tfoot style="background:#fafafa;font-weight:bold;">
                <tr>
                    <td>Total</td>
                    <td>$<?php echo number_format(array_sum(array_column($reporte, 'total')), 2, ',', '.'); ?></td>
                    <td><?php echo array_sum(array_column($reporte, 'cantidad')); ?></td>
                </tr>
            </tfoot>
        </table>
    <?php else : ?>
        <p>No se encontraron datos para el rango solicitado.</p>
    <?php endif; ?>
</div>