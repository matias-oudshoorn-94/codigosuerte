<?php
$base_url = base_url()."application/views/layouts";

session_start();

//echo($_SESSION['pago']);

$sucursal_id = $this->session->userdata("user_data")[0]->idusuario;

if (!isset($this->session->userdata("user_data")[0]->idusuario))
{
      redirect('login');
}

$servername = "localhost";
$username = "u245081402_codigosuerte";
$password = "hlD@Dh5No^5O";
$dbname = "u245081402_codigosuerte";

$conn = new mysqli($servername, $username, $password, $dbname);

mysqli_set_charset($conn, 'utf8');
// Check connection
if ($conn->connect_error)
{
    die("Falló la conexión: " . $conn->connect_error);
}

$sql456 = "SELECT caja_cerrada FROM usuarios WHERE idusuario = '".$this->session->userdata("user_data")[0]->idusuario."' ";


if ($conn->query($sql) === TRUE)
{
    //echo "New record created successfully";
}
else
{
    //echo "Error: " . $sql5 . "<br>" . $conn->error;
}

$resultado_caja = mysqli_query($conn,$sql456);


while ($fila = mysqli_fetch_array($resultado_caja))
{
    $caja_cerrada = $fila['caja_cerrada'];
}



//Si ya se cerró la caja del dia, redireccionamos a caja cerrada
if( $caja_cerrada == 1)
{
      redirect('/caja_cerrada', 'refresh');
}

?>

<style type="text/css" media="screen">
  #test_email
  {
      display: none;
  }
</style>

 <style type="text/css" media="screen">

.bg-secondary {
  background-image: url("<?php echo $base_url ?>/img/fondo-detalle.png");
  min-height: 200px;
  background-attachment: fixed;
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
 margin-bottom: 50px;

}


</style>


          <section class="page-title page-title-4 bg-secondary" style="margin-bottom: 0px;">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <h3 class="uppercase mb0" style="color:#ffffff ! important">Nueva venta</h3>
                        </div>
                    </div>
                    <!--end of row-->
                </div>
                <!--end of container-->


            </section>

            <!-- Breadcrumb Starts -->
						    <ol class="breadcrumb">
						        <li><a href="<?php echo base_url()?>">Inicio</a></li>
						        <li class="active">Nueva venta</li>
						    </ol>
						<!-- Breadcrumb Ends -->

            <section style="padding: 50px 0;">
                <div class="container">
                    <div class="row">

                        <h3>Agregar productos</h3>

                        <div class="col-md-12">
                          <input type="text" value="" onKeyPress="return runScript(event)" name="texto_buscar" id="texto_buscar" type="search" placeholder="Buscar productos por nombre o codigo..." style="border: 2px solid #eaeaea;padding: .5rem 1rem;padding-right: 2rem;padding: -0.8rem 1rem;padding-right: 2rem;box-shadow: 0 0 10px 2px rgba(0,0,0,.05);width: 95%;">
                                  <button id="boton_buscar" onclick="buscar_prod()" type="button" style="position: inherit;right: 50px;   height: 100%;z-index: 4;background: none;cursor: pointer;border: none;padding: 0 1em;"><i class="ti-search"></i></button>
                        </div>



                        <div class="col-md-12" style="height: 510px;overflow: auto;">


                            <div id="resultado_productos">


                           <table class="table table-hover">
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
  <tbody>
    <?php
    $nombre_cantidad = 0;
    foreach ($rows_productos as $producto) {
        $nombre_cantidad++;
    ?>
      <tr>
        <td style="text-transform: uppercase; font-size:14px; font-weight:600;color:#000">
          <?php echo $producto->codigo; ?>
        </td>
        <td style="text-transform: uppercase; font-size:16px; font-weight:600;color:#000">
          <?php echo $producto->nombre; ?>
        </td>
        <td style="text-transform: uppercase; font-size:14px; font-weight:600;color:#000">
          $<?php echo $producto->precio_final; ?>
        </td>
        <td style="font-size:14px; font-weight:600;color:#000">
          <?php echo $producto->stock_actual; ?>
        </td>
        <td>
          <input id="<?php echo $nombre_cantidad ?>" value="1" type="number" name="quantity" min="1" max="99"
            style="height: 40px;padding-left: 20px;font-weight: 500;margin-bottom: 0px;font-size:16px; font-weight:600;color:#000;background: #d0d0d0;border: 2px solid #47b475;">
        </td>
        <td>
          <a class="btn btn-filled btn-lg"
            style="height: 40px;line-height: 39px;min-width: 150px;margin-bottom: 0px;"
            onclick="javascript: agregarArt('<?php echo $producto->id; ?>', document.getElementById('<?php echo $nombre_cantidad ?>').value);">
            Agregar
          </a>
        </td>
        <td style="width: 100px;">
          <span id="cart-success<?php echo $producto->id; ?>"></span>
        </td>
      </tr>
    <?php } ?>
  </tbody>
</table>

                             </div>

                        </div>

                    </div>
                    <!--end of row-->
                </div>
                <!--end of container-->
            </section>


            <hr>



          <section style="padding: 50px 0;">
                <div class="container">
                    <div class="row">

                      <h3>Detalles de la venta</h3>

                        <div class="col-md-12">

                          <div id="resultado">

                            <table class="table table-hover"><thead><tr><th scope="col">Nombre</th><th scope="col" style="text-align: right;">Cantidad</th><th scope="col" style="text-align: right;">Precio</th><th scope="col" style="text-align: right;">Total</th><th scope="col" style="text-align: right;">Acciones</th></tr></thead><tbody>

                           <?php
$cantidad = 0;
$total = 0;
foreach ($rows_carro as $carro):
    $cantidad += $carro->quantity;
    $subtotal = $carro->precio_final * $carro->quantity;
    $total += $subtotal;
?>
<tr>
    <td class="text-left"><?php echo htmlspecialchars($carro->nombre); ?></td>
    <td class="text-right">x <?php echo (int)$carro->quantity; ?></td>
    <td class="text-right">$<?php echo number_format($carro->precio_final, 2, ',', '.'); ?></td>
    <td class="text-right">$<?php echo number_format($subtotal, 2, ',', '.'); ?></td>
    <td class="text-right">
        <a onClick="quitarArt(<?php echo $carro->product; ?>, 0, 0)" style="cursor: pointer; font-size: 20px;">
            <i class="ti-trash" style="vertical-align: middle;"></i>
        </a>

        <input type="number"
               name="quantity"
               id="prod-<?php echo $carro->product; ?>"
               value="<?php echo (int)$carro->quantity; ?>"
               min="1"
               class="form-control"
               style="display: inline-block; border: 1px solid #b9b9b9; width: 59px; height: 30px; padding-left: 10px; margin: 0 10px; font-weight: 500; border-radius: 0;"/>

        <a onClick="modificarArt(<?php echo $carro->product; ?>)" style="cursor: pointer; font-size: 20px;">
            <i class="ti-reload" style="vertical-align: middle;"></i>
        </a>
    </td>
</tr>
<?php endforeach; ?>
                            </tbody></table>

                          </div>

                        </div>
                        <div class="col-md-12">

                          <div id="cart-total" style="font-size: 18px;">

                            <?php echo ($cantidad.' item(s) -$'.number_format($total, 2, ',', '.')); ?>

                          </div>

                        </div>

                        <div class="col-md-12" style="margin-top: 30px">

                          <div id="cart-total-bottom" style="font-size: 25px;text-align: right;">
                                  <?php echo ('<b>TOTAL:</b> $'.number_format($total, 2, ',', '.')); ?>
                          </div>

                        </div>

                    </div>

                    <div class="row" style="margin-top: 50px;">
                          <div class="col-md-12">

                          	<span style="font-family:'Raleway', 'Helvetica Neue', Helvetica, Arial, sans-serif;font-size: 11px;  text-transform: uppercase;color: #292929;letter-spacing: 1px;font-weight: 700;display: block;cursor: default;">Seleccione el medio de pago:<font style="color: red;font-size: 20px">*</font></span>

                          	<div class="select-option">
                                    <i class="ti-angle-down"></i>
                                    <select id="pago" name="pago" onchange="cambia_pago()">
                                    		<option value="0">Seleccione...</option>
                                            <option value="Efectivo">Efectivo</option>
                                            <option value="Tarjeta de crédito/débito">Tarjeta de crédito/débito</option>
                                            <option value="Medio electrónico">Medio electrónico</option>
                                            <option value="Transferencia/QR">Transferencia/QR</option>
                                    </select>
                                    </div>

                          </div>
                    </div>

                    <div class="row">
                          <div class="col-md-12" id="mensaje">

                          </div>
                    </div>

                    <div class="row" style="margin-top: 50px;">
                          <div class="col-md-12" style="text-align: right;">
                              <a class="btn btn-filled btn-lg" onclick="finalizar_compra();" >Finalizar venta</a>
                          </div>
                    </div>

                </div>
</section>

<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>

<script type="text/javascript">

$(document).ready(function()
{



});


function buscar_prod()
{

  var busqueda = $('#texto_buscar').val();
                    document.getElementById("resultado_productos").innerHTML = '<div class="col-md-12" style="text-align: center;"><p>Buscando productos...</p><img src="<?php echo $base_url ?>/img/cargando.gif" alt="Cargando Promo Colores" style="width: 50px;"></div>';

                    var sucursal = <?php echo $sucursal_id ?>;

                    $.ajax({
                        type: "POST",
                        url: '<?php echo base_url()?>/busqueda/buscar_productos.php',
                        data: {busqueda: busqueda, sucursal: sucursal },
                        success: function(data){
                            $("#resultado_productos").html(data);

                        }
                    });

}

function cambia_pago()
{
    const pago = document.getElementById('pago').value;

    $.ajax({
        type: "POST",
        url: '<?php echo base_url()?>set_pago',
        data: { pago: pago },
    });
}

function finalizar_compra()
{
	if(document.getElementById('pago').value == 0)
	{
		$("#mensaje").html('<p style="color: red;font-size: 16px;">*Por favor, seleccione un medio de pago para finalizar la venta</p>');
	}
	else
	{
		var mensaje;
        var opcion = confirm("¿Confirmar venta?");
        if (opcion == true)
        {
            /*
            location.href = '<?php echo base_url()?>enviar_pedido';
            */

            const pago = document.getElementById('pago').value;
            location.href = '<?php echo base_url()?>enviar_pedido?pago=' + encodeURIComponent(pago);
        }
        else {
            mensaje = "Cancelaste la venta";
            alert(mensaje);
        }
	}

}

function actualizar_session_pago(value)
{
        $.ajax({
            type: "POST",
            url: '<?php echo base_url()?>include/actualizar_session.php',
            data: 'select_amount=' + value,
            dataType: 'json',
            success: function (data) {

            }
        });
}


let codigoLeido = '';
let tiempoUltimoChar = 0;

document.addEventListener('keypress', function(e) {
    const tiempoActual = new Date().getTime();

    // Si pasó más de 100 ms entre teclas, empieza un nuevo código
    if (tiempoActual - tiempoUltimoChar > 100) {
        codigoLeido = '';
    }

    // Si se presiona Enter (la pistola lo envía al final del escaneo)
    if (e.key === 'Enter') {
        e.preventDefault();

        const codigo = codigoLeido.trim();
        if (codigo !== '') {
            //$('#texto_buscar').val(codigo);
            agregarProductoPorCodigo(codigo);
        }

        codigoLeido = '';
        return;
    }

    codigoLeido += e.key;
    tiempoUltimoChar = tiempoActual;
});

function agregarProductoPorCodigo(codigo) {
    $.ajax({
        type: "POST",
        url: '<?php echo base_url()?>busqueda/buscar_producto_por_codigo.php',
        data: {codigo: codigo},
        success: function(data) {
            if (data && data.trim() !== '') {
                try {
                    const producto = JSON.parse(data);
                    if (producto && producto.id) {
                        agregarArt(producto.id, 1);
                        alert('✅ Producto "' + producto.nombre + '" agregado al pedido.');
                    } else {
                        alert('⚠️ No se pudo agregar el producto (datos inválidos).');
                    }
                } catch (e) {
                    console.error('Error procesando el producto:', e);
                    alert('⚠️ Error procesando la respuesta del servidor.');
                }
            } else {
                alert('❌ No se encontró ningún producto con el código: ' + codigo);
            }
        },
        error: function() {
            alert('⚠️ Error al conectar con el servidor.');
        }
    });
}

</script>