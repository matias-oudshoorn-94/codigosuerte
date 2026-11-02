<?php
$base_url = base_url()."application/views/layouts";

session_start();

//echo($_SESSION['pago']);

//print_r($this->session->userdata("user_data")[0]);

if (!isset($this->session->userdata("user_data")[0]->idusuario))
{
      redirect('login');
}

$servername = "localhost";
$username = "c1890072_pdfadmi";
$password = "goMOsopi09";
$dbname = "c1890072_pdfadmi";

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

                        <div class="col-md-12" style="height: 510px;overflow: auto;">
                       

                          
                        
                            <table class="table table-hover">
                            <thead>
                              <tr>
                                <th scope="col">Código</th>
                                <th scope="col">Nombre</th>
                                <th scope="col">Precio</th>
                                <th scope="col">Cantidad</th>
                                <th scope="col">Acciones</th>
                                <th scope="col" style="width: 100px;"></th>
                              </tr>
                            </thead>
                            <tbody>



                            <?php 
                            $nombre_cantidad = 0;
                            foreach ($rows_productos as $producto)
                            {
                              $nombre_cantidad++;
                            ?>

                              <tr>
                                <td> <?php echo $producto->codigo; ?> </td>
                                <td> <?php echo $producto->nombre; ?> </td>
                                <td> $<?php echo $producto->precio_final; ?> </td>
                                <td> <input id="<?php echo $nombre_cantidad ?>" type="number" name="quantity" min="1" max="99" style="height: 40px;padding-left: 20px;font-weight: 500;margin-bottom: 0px;"> </td>
                                <td> <a class="btn btn-filled btn-lg" style="height: 40px;line-height: 39px;min-width: 150px;margin-bottom: 0px;" onclick="javacript: agregarArt('<?php echo $producto->id; ?>', document.getElementById('<?php echo $nombre_cantidad ?>').value);" >Agregar</a> 
                                </td>
                                <td style="width: 100px;">
                                <span id="cart-success<?php echo $producto->id; ?>"></span>
                                </td> 
                              </tr>

                          	<?php }?>
                              
                            </tbody>
                            </table>

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
                              
                            <?php foreach($rows_carro as $carro)
                            {
                              $cantidad = $cantidad+$carro->quantity*1;
                              $total += $carro->precio_final*$carro->quantity; 
                              ?>
                              <tr>
                                <td class="text-left"><a href=""><?php echo $carro->nombre?></a></td>
                                <td class="text-right">x <?php echo $carro->quantity?></td>
                                <td class="text-right">$<?php echo $carro->precio_final?></td>
                                <td class="text-right">$<?php echo $carro->precio_final*$cantidad?></td>
                                <td class="text-right">
                                  <a onClick="javascript: quitarArt(<?php echo $carro->product?>, 0, 0)" style="cursor: pointer;font-size: 20px;"><i class="ti-trash" style="vertical-align: middle;"></i></a>
                                  <input type="number" name="quantity" id="<?php echo $carro->product?>" value="<?php echo $carro->quantity?>" size="1" class="form-control" style="display: initial;border: 1px solid #b9b9b9;width: 59px;height: 30px;padding-left: 10px;margin-left: 10px;font-weight: 500;margin-bottom: 0px;border-radius: 0;margin-right: 10px"/>
                                  <a onClick="javascript: modificarArt(<?php echo $carro->product?>, 0)" style="cursor: pointer;font-size: 20px;"><i class="ti-reload" style="vertical-align: middle;"></i></a></td></tr>

                            <?php
                            }?>
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
	//eventos al cargar la pagina
    
});


function cambia_pago()
{
	actualizar_session_pago(document.getElementById('pago').value);
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
            location.href = '<?php echo base_url()?>enviar_pedido';
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

</script>