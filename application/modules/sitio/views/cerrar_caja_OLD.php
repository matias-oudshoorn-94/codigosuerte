<?php
$base_url = base_url()."application/views/layouts";


//print_r($rows_impresoras);


$servername = "localhost";
$username = "pdfcolor_codigosuerte";
$password = "jB&vb!B~sr+5ZPd+";
$dbname = "pdfcolor_codigosuerte";

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

$cantidad_impresoras_a_declarar = 0;
foreach ($rows_impresoras as $impresora)
{
	$cantidad_impresoras_a_declarar++;
}

?>

<style type="text/css" media="screen">
  #test_email
  {
      display: none;
  }

.loader {
    position: fixed;
    left: 0px;
    top: 0px;
    width: 100%;
    height: 100%;
    z-index: 9999;
    background: url('<?php echo $base_url ?>/img/cargando.gif') 50% 50% no-repeat rgb(249,249,249);
    opacity: .8;
}

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

<div class="loader" id="loader"></div>
          <section class="page-title page-title-4 bg-secondary" style="margin-bottom: 0px;">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <h3 class="uppercase mb0" style="color:#ffffff ! important">Cerrar caja</h3>
                        </div>
                    </div>
                    <!--end of row-->
                </div>
                <!--end of container-->


            </section>

            <!-- Breadcrumb Starts -->
						    <ol class="breadcrumb">
						        <li><a href="<?php echo base_url()?>">Inicio</a></li>
						        <li class="active">Cerrar caja</li>
						    </ol>
						<!-- Breadcrumb Ends -->

            <section style="padding: 50px 0;">
                <div class="container">
                    <div class="row">

                        <div class="col-md-6">

                          <?php
                          $cantidad_pedidos = 0;
                          $total = 0;

                          $total_efectivo = 0;
                          $total_tarjeta = 0;
                          $total_electronico = 0;
                          $total_transferencia = 0;

                          foreach ($rows_pedidos as $pedidos)
                          {
                            $cantidad_pedidos++;
                            $total = $total + $pedidos->total;
                            if($pedidos->modo_de_pago == 'Efectivo')
                              {
                                  $total_efectivo = $total_efectivo+$pedidos->total;
                              }
                              if($pedidos->modo_de_pago == 'Tarjeta de crédito/débito')
                              {
                                  $total_tarjeta = $total_tarjeta+$pedidos->total;
                              }
                              if($pedidos->modo_de_pago == 'Medio electrónico')
                              {
                                  $total_electronico = $total_electronico+$pedidos->total;
                              }
                              if($pedidos->modo_de_pago == 'Transferencia/QR')
                              {
                                  $total_transferencia = $total_transferencia+$pedidos->total;
                              }
                          }
                          ?>

                          <h2>Ventas del día</h2>

                          <h6 style="font-size: 17px;font-weight: 500;margin-bottom: 3px;">Cantidad de ventas: <font style="font-size: 16px;font-weight: 400"><?php echo $cantidad_pedidos ?></font></h6>

                          <h6 style="font-size: 17px;font-weight: 500;margin-bottom: 3px;">Cantidad de impresiones aproximadas: <font style="font-size: 16px;font-weight: 400" id="impresiones"></font></h6>

                          <h6 style="font-size: 17px;font-weight: 500;margin-bottom: 3px;">Efectivo: <font style="font-size: 16px;font-weight: 400">$<?php echo number_format($total_efectivo, 2, ',', '.') ?></font></h6>

                          <h6 style="font-size: 17px;font-weight: 500;margin-bottom: 3px;">Tarjetas de crédito/débito: <font style="font-size: 16px;font-weight: 400">$<?php echo number_format($total_tarjeta, 2, ',', '.') ?></font></h6>

                          <h6 style="font-size: 17px;font-weight: 500;margin-bottom: 10px;">Medio electrónico: <font style="font-size: 16px;font-weight: 400">$<?php echo number_format($total_electronico, 2, ',', '.') ?></font></h6>

                          <h6 style="font-size: 17px;font-weight: 500;margin-bottom: 10px;">Transferencia/QR: <font style="font-size: 16px;font-weight: 400">$<?php echo number_format($total_transferencia, 2, ',', '.') ?></font></h6>

                          <h6 style="font-size: 25px;font-weight: 600;margin-bottom: 30px;">TOTAL: <font style="font-size: 30px;font-weight: 600">$<?php echo number_format($total, 2, ',', '.') ?></font></h6>

                        </div>
                        <div class="col-md-6">



                          <h2>Impresoras</h2>

                          <?php foreach ($rows_impresoras as $impresora)
                          {?>
                            <div style="font-size: 16px;" class="contenedor_impresora_<?php echo $impresora->codigo?>" id="contenedor_impresora_<?php echo $impresora->codigo?>">

                               <h6 style="font-size: 17px;font-weight: 500;margin-bottom: 3px;">Código: <font style="font-size: 16px;font-weight: 400"><?php echo $impresora->codigo?></font></h6>

                               <h6 style="font-size: 17px;font-weight: 500;margin-bottom: 3px;">Fecha de alta: <font style="font-size: 16px;font-weight: 400"><?php echo $impresora->fecha?></font></h6>


                               <h6 style="font-size: 17px;font-weight: 500;margin-bottom: 3px;">Cantidad de copias actuales: <font style="font-size: 16px;font-weight: 400"><?php echo $impresora->copias?></font></h6>

                               <input id="cantidad-<?php echo $impresora->codigo ?>" type="number" name="quantity" min="1" max="99" style="height: 40px;padding-left: 20px;font-weight: 500;margin-bottom: 10px;" placeholder="Declarar cantidad de impresiones">

                               <a id="boton-impresora-<?php echo $impresora->codigo ?>" class="btn btn-filled btn-lg" style="height: 40px;line-height: 39px;min-width: 150px;margin-bottom: 0px;" onclick="javacript: declararImpresora(<?php echo $impresora->copias; ?>,'<?php echo $impresora->codigo; ?>', document.getElementById('cantidad-<?php echo $impresora->codigo ?>').value);" >Declarar impresiones</a>

                            <div id="resultado_impresoras_<?php echo $impresora->codigo?>"></div>


                             </div>



                             <hr style="margin-top: 15px;margin-bottom: 15px;">

                          <?php
                          }
                          ?>

                        </div>

                        <input type="number" name="cantidad_impresoras_oculto" id="cantidad_impresoras_oculto" style="display: none">
                        <input type="number" name="cantidad_impresiones_oculto" id="cantidad_impresiones_oculto" value="0" style="display: none;">
                        <input type="number" name="cantidad_impresiones_aprox" id="cantidad_impresiones_aprox" style="display: none">
                        <input type="number" name="cantidad_desperdicio" id="cantidad_desperdicio" style="display: none">




                    </div>
                    <!--end of row-->
                    <div class="row">
                    	<div class="col-md-12">
                    		<div id="desperdicio">

                    		</div>
                    	</div>
                    </div>
                    <div class="row">
                      <div class="col-md-12">
                        <div id="cierre_de_caja">

                        </div>
                      </div>
                    </div>
                     <div class="row" style="margin-top: 50px;">

                          <div class="col-md-12" style="text-align: right">

                              <a class="btn btn-filled btn-lg" onclick="cerrarCaja();" id="boton_cerrar" style="display: none;">Cerrar caja de hoy</a>
                          </div>
                    </div>

                </div>
                <!--end of container-->
            </section>


<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>



<script type="text/javascript">

$(document).ready(function()
{
  obtener_impresiones_aprox();
  $("#cantidad_impresoras_oculto").val('<?php echo $cantidad_impresoras_a_declarar ?>');

});


function obtener_impresiones_aprox()
{
  var codigo_cliente = '<?php echo $this->session->userdata("user_data")[0]->idusuario ?>';

          $.ajax({
              type: "POST",
              url: '<?php echo base_url()?>include/impresiones_aprox.php',
              data: {codigo_cliente: codigo_cliente},
              success: function (data) {

              $("#impresiones").html(data);
              $("#cantidad_impresiones_aprox").val(data);

              }
          });
}


function declararImpresora($copias_actuales, $codigo, $cantidad)
{

  var mensaje;
  var opcion = confirm('¿Confirmar declaración de impresora?');

  if (opcion == true)
  {

    if($cantidad <= $copias_actuales || $cantidad <= 0 || $cantidad == '')
    {
        $('#resultado_impresoras_'+$codigo+'').html('<div style="padding: 8px;margin-top: 10px;font-weight: 700;color: #f52626;text-align: center;font-size: 15px;background-color: #ffd6d6;"><i class="ti-close" style="margin-right:10px"></i>La cantidad de impresiones ingresada es incorrecta</div>');
    }

    else
    {
          document.getElementById('loader').style.display = "block";

          var codigo_cliente = '<?php echo $this->session->userdata("user_data")[0]->idusuario ?>';
          var codigo_impresora = $codigo;
          var cantidad_impresiones_hoy = $copias_actuales;
          var cantidad_total = $cantidad;


          $.ajax({
              type: "POST",
              url: '<?php echo base_url()?>include/declarar_impresora.php',
              data: {codigo_cliente: codigo_cliente, codigo_impresora: codigo_impresora, cantidad_impresiones_hoy: cantidad_impresiones_hoy, cantidad_total: cantidad_total},
              success: function (data) {

	              var valor_input = $("#cantidad_impresoras_oculto").val();
	              var valor_impresiones = $("#cantidad_impresiones_oculto").val();
	              var calculo = parseInt(valor_impresiones) + parseInt($cantidad - cantidad_impresiones_hoy);
	              var impresiones_aprox = $("#cantidad_impresiones_aprox").val();
	              var desperdicio = impresiones_aprox * 100 / calculo;
	              desperdicio = 100 - desperdicio;

                $("#cantidad_desperdicio").val(desperdicio);


	              $("#cantidad_impresoras_oculto").val(valor_input - 1);
				        $("#cantidad_impresiones_oculto").val(calculo);

	              document.getElementById('loader').style.display = "none";
	              $('#contenedor_impresora_'+codigo_impresora+'').html(data);

	              if($("#cantidad_impresoras_oculto").val()==0)//Si ya no quedan impresoras por declarar
	              {
	              	document.getElementById('boton_cerrar').style.display = "inline-block"; // Muestro el boton de cerrar caja
	              	if(desperdicio <= 10)
	              	{
	              		$('#desperdicio').html('<div style="padding: 20px;margin-top: 10px;font-weight: 700;color: #5dc55d;text-align: center;font-size: 25px;background-color:#d3ffb1;"><i class="ti-face-smile" style="margin-right:10px"></i>Su desperdicio de hoy fué del: '+desperdicio.toFixed(2)+'%</div>');



	              	}
	              	else
	              	{
	              		$('#desperdicio').html('<div style="padding: 20px;margin-top: 10px;font-weight: 700;color: #f52626;text-align: center;font-size: 25px;background-color: #ffd6d6;"><i class="ti-face-sad" style="margin-right:10px"></i>Su desperdicio de hoy fué del: '+desperdicio.toFixed(2)+'%</div>');
	              	}
	              }



              }
          });
    }
  }
  else
  {
    mensaje = "Cancelaste la declaración de impresora";
    alert(mensaje);
  }

}




function cerrarCaja($codigo)
{

  var mensaje;
  var opcion = confirm('¿Confirma el cierre de caja diario?');

  if (opcion == true)
  {

          document.getElementById('loader').style.display = "block";

          var codigo_cliente = '<?php echo $this->session->userdata("user_data")[0]->idusuario ?>';
          var efectivo = '<?php echo $total_efectivo; ?>';
          var tarjeta = '<?php echo $total_tarjeta; ?>';
          var electronico = '<?php echo $total_electronico; ?>';
          var transferencia = '<?php echo $total_transferencia; ?>';
          var total = '<?php echo $total; ?>';
          var impresiones_reales = $("#cantidad_impresiones_oculto").val();
          var impresiones_aprox = $("#cantidad_impresiones_aprox").val();
          var cantidad_ventas = '<?php echo $cantidad_pedidos; ?>';
          var desperdicio = $("#cantidad_desperdicio").val();



          $.ajax({
              type: "POST",
              url: '<?php echo base_url()?>include/cerrar_caja.php',
              data: {codigo_cliente: codigo_cliente, efectivo: efectivo, tarjeta: tarjeta, electronico: electronico, transferencia: transferencia, total: total, impresiones_aprox: impresiones_aprox, impresiones_reales: impresiones_reales, cantidad_ventas: cantidad_ventas, desperdicio: desperdicio},
              success: function (data) {

              //$('#resultado-impresora').html(data);
              document.getElementById('loader').style.display = "none";
              $('#cierre_de_caja').html(data);

              }
          });

  }
  else
  {
    mensaje = "Cancelaste el cierre de caja";
    alert(mensaje);
  }

}



$(window).load(function() {
    $(".loader").fadeOut("slow");
});


</script>