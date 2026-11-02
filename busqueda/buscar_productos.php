<?php

include("include/configuration.php");

session_start();

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Falló la conexión: " . $conn->connect_error);
}

$buscar = $conn->real_escape_string($_POST['busqueda']);
$sucursal = $_POST['sucursal'];

$id_sucursal = 0;
if ($sucursal) {
    $id_sucursal = $sucursal;
}

$sql = "SELECT * FROM productos WHERE nombre LIKE '%".$buscar."%' OR codigo LIKE '%".$buscar."%'";
$result = $conn->query($sql);

$cantidad = 0;
$nombre_cantidad = 0;

echo '<table class="table table-hover">
        <thead>
          <tr>
            <th scope="col">Código</th>
            <th scope="col">Nombre</th>
            <th scope="col">Precio</th>
            <th scope="col">Stock</th>
            <th scope="col">Cantidad</th>
            <th scope="col">Acciones</th>
            <th scope="col" style="width: 100px;"></th>
          </tr>
        </thead>
        <tbody>';

while ($fila = mysqli_fetch_assoc($result)) {
    $nombre_cantidad++;

    $sql_stock = "SELECT stock FROM stock_sucursal WHERE producto_id = '".$fila['id']."' AND idusuario = '".$id_sucursal."' LIMIT 1";
    $res_stock = $conn->query($sql_stock);
    $stock_actual = 0;
    if ($res_stock && $res_stock->num_rows > 0) {
        $stock_row = $res_stock->fetch_assoc();
        $stock_actual = $stock_row['stock'];
    }

    echo '<tr>
            <td style="text-transform: uppercase; font-size:14px; font-weight:600;color:#000">'.$fila['codigo'].'</td>
            <td style="text-transform: uppercase; font-size:16px; font-weight:600;color:#000">'.iconv('ISO-8859-1', 'UTF-8', $fila['nombre']).'</td>
            <td style="text-transform: uppercase; font-size:14px; font-weight:600;color:#000">'.$fila['precio_final'].'</td>
            <td style="font-size:14px; font-weight:600;color:#000">'.$stock_actual.'</td>
            <td><input id="'.$nombre_cantidad.'" value="1" type="number" name="quantity" min="1" max="99" style="height: 40px;padding-left: 20px;font-weight: 500;margin-bottom: 0px;font-size:16px; font-weight:600;color:#000;background: #d0d0d0;border: 2px solid #47b475;"></td>
            <td><a class="btn btn-filled btn-lg" style="height: 40px;line-height: 39px;min-width: 150px;margin-bottom: 0px;" onclick="javascript: agregarArt('.$fila['id'].', document.getElementById('.$nombre_cantidad.').value);">Agregar</a></td>
            <td style="width: 100px;"><span id="cart-success'.$fila['id'].'"></span></td>
          </tr>';

    $cantidad++;
}

echo '</tbody></table>';

if($cantidad == 0) {
    echo "<div class='col-md-12' style='text-align:center'><p>No se encontraron productos.</p></div>";
}
?>