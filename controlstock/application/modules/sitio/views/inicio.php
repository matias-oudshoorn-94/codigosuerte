<?php
$base_url = base_url()."application/views/layouts";

//print_r($this->session->userdata("user_data")[0]);

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


          <section class="page-title page-title-4 bg-secondary" style="margin-bottom: 20px;">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <h3 class="uppercase mb0" style="color:#ffffff ! important">Sucursal <?php echo $this->session->userdata("user_data")[0]->localidad ?> - (<?php echo $this->session->userdata("user_data")[0]->domicilio ?>)</h3>
                        </div>
                    </div>
                    <!--end of row-->
                </div>
                <!--end of container-->
            </section>

            <section style="padding: 50px 0;">
                <div class="container">
                    <div class="row">

                        <div class="col-sm-6 col-md-6" style="text-align: center;">
                            <a class="btn btn-filled btn-lg" href="<?php echo base_url() ?>nueva_venta">Nueva Venta</a>
                        </div>
                        <div class="col-sm-6 col-md-6" style="text-align: center;">
                             <a class="btn btn-filled btn-lg" href="<?php echo base_url() ?>cerrar_caja">Cerrar Caja</a>
                        </div>
                        </div>
                    </div>
                    <!--end of row-->
                </div>
                <!--end of container-->
            </section>
