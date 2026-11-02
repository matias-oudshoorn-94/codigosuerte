<div class="contenedor-reportes" style="background:#f9f9f9;padding:30px;border:1px solid #ccc; border-radius:8px;">
    <h1 style="text-align:center;margin-bottom:30px;">Panel de Reportes</h1>

    <!-- REPORTE PARCIAL -->
    <div class="reporte-seccion" style="margin-bottom:40px; background:#fff; padding:20px; border-radius:6px; border:1px solid #ddd;">
        <h3>Reporte Parcial</h3>
        <p>Para generar el reporte parcial, seleccione la sucursal, la fecha desde y la fecha hasta. Una vez generado, será redirigido a la seccion de "Reportes parciales" para ver el resultado de su búsqueda.</p>
        <form method="get" action="<?php echo site_url('admin/reportes/parcial'); ?>" style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;">
            <div>
                <label for="id_usuario">Sucursal:</label>
                <select name="id_usuario" id="id_usuario" required>
                    <option value="">Seleccione...</option>
                    <?php foreach ($usuarios as $u): ?>
                        <option value="<?= $u->idusuario; ?>"><?= $u->nombre . " " . $u->apellido . " (" . $u->localidad . ")"; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="fecha_desde">Desde:</label>
                <input type="date" name="fecha_desde" id="fecha_desde" required>
            </div>
            <div>
                <label for="fecha_hasta">Hasta:</label>
                <input type="date" name="fecha_hasta" id="fecha_hasta" required>
            </div>
            <button type="submit" class="btn btn-inverse" style="margin-bottom:9px;margin-left:10px;">Generar</button>
        </form>
    </div>

    <!-- REPORTE MENSUAL -->
<div class="reporte-seccion" style="margin-bottom:40px; background:#fff; padding:20px; border-radius:6px; border:1px solid #ddd;">
    <h3>Reporte Mensual</h3>
    <p>Para generar el mensual, seleccione la sucursal y el mes/año. Una vez generado, será redirigido a la sección de "Reportes mensuales" para ver el resultado de su búsqueda.</p>
    <form method="get" action="<?php echo site_url('admin/reportes/mensual'); ?>" style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;">
        <div>
            <label for="id_usuario_mensual">Sucursal:</label>
            <select name="id_usuario" id="id_usuario_mensual" required>
                <option value="">Seleccione...</option>
                <?php foreach ($usuarios as $u): ?>
                    <option value="<?= $u->idusuario; ?>"><?= $u->nombre . " " . $u->apellido . " (" . $u->localidad . ")"; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="mes_anio">Mes y Año:</label>
<input type="month" name="mes_anio" id="mes_anio" value="<?= date('Y-m'); ?>" min="2020-01" max="<?= date('Y-m'); ?>" required>

        </div>
        <button type="submit" class="btn btn-inverse" style="margin-bottom:9px; margin-left:10px;">Generar</button>
    </form>
</div>

    <!-- REPORTE GRÁFICO -->
    <div class="reporte-seccion" style="margin-bottom:40px; background:#fff; padding:20px; border-radius:6px; border:1px solid #ddd;">
        <h3>Gráfico de ventas</h3>
        <form method="get" action="<?php echo site_url('admin/reportes/grafico'); ?>" style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;">
            <div>
                <label for="id_usuario_grafico">Sucursal:</label>
                <select name="id_usuario" id="id_usuario_grafico" required>
                    <option value="">Seleccione...</option>
                    <?php foreach ($usuarios as $u): ?>
                        <option value="<?= $u->idusuario; ?>"><?= $u->nombre . " " . $u->apellido . " (" . $u->localidad . ")"; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="limite">Días a mostrar:</label>
                <input type="number" name="limite" id="limite" value="7" min="1" max="30">
            </div>
            <button type="submit" class="btn btn-inverse" style="margin-bottom:9px;margin-left:10px;">Generar</button>
        </form>
    </div>

    <!-- COMPARATIVO DE SUCURSALES -->

    <div class="reporte-seccion" style="margin-bottom:40px; background:#fff; padding:20px; border-radius:6px; border:1px solid #ddd;">
    <h3>Comparativo de sucursales</h3>
    <p>En esta sección, se puede generar un grafico comparativo de las ventas de todas las sucursales, seleccionando la cantidad de dias a comparar.</p>
<form method="get" action="<?php echo site_url('admin/reportes/comparativo'); ?>">
    <label for="limite_comparativo">Días a mostrar:</label>
    <input type="number" name="limite" id="limite_comparativo" value="7" min="1" max="30">

    <button type="submit" class="btn btn-inverse" style="margin-bottom:9px;margin-left:10px;">Ver comparativo</button>
</form>
                    </div>
</div>