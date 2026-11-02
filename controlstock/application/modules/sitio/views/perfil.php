<?php
$base_url = base_url()."application/views/layouts";


//print_r($rows_impresoras);

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
                            <h3 class="uppercase mb0" style="color:#ffffff ! important">Mis datos</h3>
                        </div>
                    </div>
                    <!--end of row-->
                </div>
                <!--end of container-->


            </section>

            <!-- Breadcrumb Starts -->
						    <ol class="breadcrumb">
						        <li><a href="<?php echo base_url()?>">Inicio</a></li>
						        <li class="active">Mis datos</li>
						    </ol>
						<!-- Breadcrumb Ends -->

            <section style="padding: 50px 0;">
                <div class="container">
                    <div class="row">



                        <div class="col-md-4">
                          <h2>Encargado</h2>

                          <h6 style="font-size: 17px;font-weight: 500;margin-bottom: 3px;">Nombre: <font style="font-size: 16px;font-weight: 400"><?php echo $this->session->userdata("user_data")[0]->nombre; ?></font></h6>

                          <h6 style="font-size: 17px;font-weight: 500;margin-bottom: 3px;">Apellido: <font style="font-size: 16px;font-weight: 400"><?php echo $this->session->userdata("user_data")[0]->apellido; ?></font></h6>

                          <h6 style="font-size: 17px;font-weight: 500;margin-bottom: 3px;">Teléfono: <font style="font-size: 16px;font-weight: 400"><?php echo $this->session->userdata("user_data")[0]->telefono; ?></font></h6>

                          <h6 style="font-size: 17px;font-weight: 500;margin-bottom: 3px;">Email: <font style="font-size: 16px;font-weight: 400"><?php echo $this->session->userdata("user_data")[0]->email; ?></font></h6>

                          <h6 style="font-size: 17px;font-weight: 500;margin-bottom: 3px;">Usuario: <font style="font-size: 16px;font-weight: 400"><?php echo $this->session->userdata("user_data")[0]->usuario; ?></font></h6>

                          <h6 style="font-size: 17px;font-weight: 500;margin-bottom: 3px;">Contraseña: <font style="font-size: 16px;font-weight: 400"><?php echo $this->session->userdata("user_data")[0]->clave; ?></font></h6>
                        </div>

                        <div class="col-md-4">
                          <h2>Impresoras</h2>

                          <?php foreach ($rows_impresoras as $impresora)
                          {?>
                               <h6 style="font-size: 17px;font-weight: 500;margin-bottom: 3px;">Código: <font style="font-size: 16px;font-weight: 400"><?php echo $impresora->codigo?></font></h6>

                               <h6 style="font-size: 17px;font-weight: 500;margin-bottom: 3px;">Fecha de alta: <font style="font-size: 16px;font-weight: 400"><?php echo $impresora->fecha?></font></h6>


                               <h6 style="font-size: 17px;font-weight: 500;margin-bottom: 3px;">Cantidad de copias: <font style="font-size: 16px;font-weight: 400"><?php echo $impresora->copias?></font></h6>

                               <hr style="margin-top: 15px;margin-bottom: 15px;">

                          <?php
                          }
                          ?>

                        </div>
                      
                        <div class="col-md-4">
                          <h2>Sucursal</h2>

                          <h6 style="font-size: 17px;font-weight: 500;margin-bottom: 3px;">Localidad: <font style="font-size: 16px;font-weight: 400"><?php echo $this->session->userdata("user_data")[0]->localidad; ?></font></h6>

                          <h6 style="font-size: 17px;font-weight: 500;margin-bottom: 3px;">Dirección: <font style="font-size: 16px;font-weight: 400"><?php echo $this->session->userdata("user_data")[0]->domicilio; ?></font></h6>
                        </div>

                    </div>
                    <!--end of row-->
                </div>
                <!--end of container-->
            </section>

