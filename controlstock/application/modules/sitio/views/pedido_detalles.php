<?php
$base_url = base_url()."application/views/layouts";

//print_r($this->session->userdata("user_data")[0]);

//print_r($rows_pedidos);

if (!isset($this->session->userdata("user_data")[0]->idusuario))
{
      redirect('login');
}

$fecha = '';
$total = 0;

foreach ($rows_detalle as $prod)
{
  $fecha = $prod->fecha;
  $total = $prod->total;
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
                            <h3 class="uppercase mb0" style="color:#ffffff ! important">Detalles de la venta</h3>
                        </div>
                    </div>
                    <!--end of row-->
                </div>
                <!--end of container-->


            </section>

            <!-- Breadcrumb Starts -->
						    <ol class="breadcrumb">
                    <li><a href="<?php echo base_url()?>">Inicio</a></li>
						        <li><a href="<?php echo base_url()?>pedidos">Ventas realizadas</a></li>
						        <li class="active">Detalles de la venta</li>
						    </ol>
						<!-- Breadcrumb Ends -->

            <section style="padding: 50px 0;">
                <div class="container">
                    <div class="row">



                        <div class="col-md-12">
                       

                          <h3>Detalles de la venta: (<?php echo $fecha ?>)</h3>
                        
                            <table class="table table-hover">
                            <thead>
                              <tr>
                                <th scope="col">Código</th>
                                <th scope="col">Nombre</th>
                                <th scope="col">Cantidad</th>
                              </tr>
                            </thead>
                            <tbody>



                            <?php 
                            foreach ($rows_detalle as $prod)
                            {                              
                            ?>

                             <tr>
                                 <td><?php echo $prod->codigo; ?></td>
                                 <td><?php echo $prod->nombre; ?></td>
                                 <td><?php echo $prod->cantidad; ?></td>
                              
                              </tr>

                          	<?php }?>
                              
                            </tbody>
                            </table>

                        </div>

                        <div class="col-md-12">

                          <div id="cart-total-bottom" style="font-size: 25px;text-align: right;">
                                  <?php echo ('<b>TOTAL:</b> $'.number_format($total, 2, ',', '.')); ?>
                          </div>

                        </div>

                    </div>
                    <!--end of row-->
                </div>
                <!--end of container-->
            </section>

