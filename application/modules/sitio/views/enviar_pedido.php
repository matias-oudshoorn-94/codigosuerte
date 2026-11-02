<?php
$base_url = base_url()."application/views/layouts";

session_start();

//echo($_SESSION['pago']);

//print_r($this->session->userdata("user_data")[0]);

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
                            <h3 class="uppercase mb0" style="color:#ffffff ! important">Venta generada</h3>
                        </div>
                    </div>
                    <!--end of row-->
                </div>
                <!--end of container-->


            </section>

            <!-- Breadcrumb Starts -->
                            <ol class="breadcrumb">
                                <li><a href="<?php echo base_url()?>">Inicio</a></li>
                                <li class="active">Venta generada</li>
                            </ol>
                        <!-- Breadcrumb Ends -->

            <section style="padding: 50px 0;">
                <div class="container">
                    <div class="row">

                        <div class="col-md-12" style="text-align: center;    margin-bottom: 50px;">
                            <h3>¡Su venta fué generada con éxito!</h3>
                        </div>


                    </div>
                    <div class="row">
                        <div class="col-sm-6 col-md-6" style="text-align: center;">
                            <a class="btn btn-filled btn-lg" href="<?php echo base_url() ?>nueva_venta">Nueva Venta</a>
                        </div>
                        <div class="col-sm-6 col-md-6" style="text-align: center;">
                             <a class="btn btn-filled btn-lg" href="<?php echo base_url() ?>pedidos">Ver ventas</a>
                        </div>
                    </div>
                    <!--end of row-->
                </div>
                <!--end of container-->
            </section>
