<?php
$base_url = base_url()."application/views/layouts";


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

#boton
{
    color: white;
    height: 60px;
    width: 250px;
    margin: auto;
    top: 0;
    left: 0;
    right: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    font-family: "Raleway", "Helvetica Neue", Helvetica, Arial, sans-serif;
    border: 2px solid #47b475;
    line-height: 36px;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    border-radius: 0;
    color: #47b475;
    text-align: center;
    transition: all 0.3s ease;
    -webkit-transition: all 0.3s ease;
    -moz-transition: all 0.3s ease;

}

#boton:hover
{
    background-color: #47b475;
    color: #FFFFFF;
}

    
</style>


<section class="page-title page-title-4 bg-secondary">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="uppercase mb0" style="color:#ffffff ! important">Caja Cerrada</h3>
                        </div>
                    </div>
                    <!--end of row-->
                </div>
                <!--end of container-->
            </section>
            <section style="    padding: 0px 0px 50px 0px;">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12" style="text-align: center;">
                            <h4 class="uppercase">¡Su caja del dia de hoy fué cerrada con éxito!</h4>
                            <p style="font-size: 15px;">
                                Tenga en cuenta que solo puede cerrar una caja por día y volver a hacerlo recién al día siguiente. Muchas gracias.
                            </p>
                        </div>
                    </div>
                    <!--end of row-->
                </div>
                <!--end of container-->
            </section>
