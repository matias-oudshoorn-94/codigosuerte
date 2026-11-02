<?php
$base_url = base_url()."application/views/layouts";

//print_r($this->session->userdata("user_data")[0]);

//print_r($rows_pedidos);

if (!isset($this->session->userdata("user_data")[0]->idusuario))
{
      redirect('login');
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
                            <h3 class="uppercase mb0" style="color:#ffffff ! important">Ventas realizadas</h3>
                        </div>
                    </div>
                    <!--end of row-->
                </div>
                <!--end of container-->


            </section>

            <!-- Breadcrumb Starts -->
						    <ol class="breadcrumb">
						        <li><a href="<?php echo base_url()?>">Inicio</a></li>
						        <li class="active">Ventas realizadas</li>
						    </ol>
						<!-- Breadcrumb Ends -->

            <section style="padding: 50px 0;">
                <div class="container">
                    <div class="row">

                        <div class="col-md-12">
                       

                          <h3>Ventas realizadas</h3>

                          <p style="font-size: 17px;">Cantidad de ventas del dia: <?php echo count($rows_pedidos);?></p>

                          <div>
                        
                            <table class="table table-hover">
                            <thead>
                              <tr>
                                <th scope="col">Fecha y hora</th>
                                <th scope="col">Modo de pago</th>
                                <th scope="col">Total</th>
                              </tr>
                            </thead>
                            <tbody>



                            <?php 
                            $nombre_cantidad = 0;
                            $total_diario = 0;

                            $total_efectivo = 0;
                            $total_tarjeta = 0;
                            $total_electronico = 0;
                            $total_transferencia = 0;

                            foreach ($rows_pedidos as $venta)
                            {
                              $nombre_cantidad++;
                              $total_diario = $total_diario + $venta->total;
                              if($venta->modo_de_pago == 'Efectivo')
                              {
                                  $total_efectivo = $total_efectivo+$venta->total;
                              }
                              if($venta->modo_de_pago == 'Tarjeta de crédito/débito')
                              {
                                  $total_tarjeta = $total_tarjeta+$venta->total;
                              }
                              if($venta->modo_de_pago == 'Medio electrónico')
                              {
                                  $total_electronico = $total_electronico+$venta->total;
                              }
                              if($venta->modo_de_pago == 'Transferencia/QR')
                              {
                                  $total_transferencia = $total_transferencia+$venta->total;
                              }

                            ?>

                             <tr>
                                 <td><a href="<?php echo base_url() ?>pedido_detalles/<?php echo $venta->idpedido; ?>"> <?php echo $venta->fecha_hora; ?> </a></td>
                                 <td><?php echo $venta->modo_de_pago; ?></td>
                                <td> $<?php echo number_format($venta->total, 2, ',', '.'); ?> </td>
                              </tr>

                          	<?php }?>
                              
                            </tbody>
                            </table>


                        </div>

                    </div>

                    <div class="col-md-12" style="margin-top: 30px">

                          <div id="cart-total-bottom" style="font-size: 25px;text-align: right;">
                                  <?php echo ('<b><font style="font-size:15px">Efectivo:</b> $'.number_format($total_efectivo, 2, ',', '.'). '</font><br>'); ?>
                                  <?php echo ('<b><font style="font-size:15px">Tarjeta débito/crédito:</b> $'.number_format($total_tarjeta, 2, ',', '.'). '</font><br>'); ?>
                                  <?php echo ('<b><font style="font-size:15px">Medio electrónico:</b> $'.number_format($total_electronico, 2, ',', '.'). '</font><br>'); ?>
                                  <?php echo ('<b><font style="font-size:15px">Transferencia/QR:</b> $'.number_format($total_transferencia, 2, ',', '.'). '</font><br><br>'); ?>
                                  <?php echo ('<b>TOTAL:</b> $'.number_format($total_diario, 2, ',', '.')); ?>
                          </div>

                    </div>

                    <div class="row" style="margin-top: 50px;">
                          <div class="col-md-12" style="text-align: right;margin-top: 50px">
                              <a class="btn btn-filled btn-lg" href="<?php echo base_url() ?>cerrar_caja" >Cerrar caja de hoy</a> 
                          </div>  
                    </div>
                    <!--end of row-->
                </div>
                <!--end of container-->
            </section>
