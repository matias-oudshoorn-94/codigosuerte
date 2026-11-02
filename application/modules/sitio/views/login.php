<?php 
$base_url = base_url()."application/views/layouts";
?>


<style>
    .image-bg a, .bg-primary a {
    color: #47b475;
}
</style>

<section class="cover fullscreen image-bg overlay">
                <div class="background-image-holder">
                    <img alt="image" class="background-image" src="<?php echo $base_url ?>/img/home12.jpg" />
                </div>
                <div class="container v-align-transform">
                    <div class="row">
                        <div class="col-md-4 col-md-offset-4 col-sm-8 col-sm-offset-2">
                            <div class="feature bordered text-center">
                                <h4 class="uppercase">Ingresar</h4>
                                <form class="text-left"  action="<?php echo base_url() ?>loginProc" method="post">
                                    <input class="mb0" type="text" name="usuario" value="" id="emmail_login" placeholder="Usuario" autocomplete="username" required/>
                                    <input class="mb0" name="clave" value="" id="password_login" type="password" placeholder="Contraseña" />
                                    <p style="color:#C5202B;">
                                     <?php if (isset($msg)) {echo $msg; } ?>
                                     </p>
                                    <input type="submit" value="Ingresar" />
                                </form>
                                <!--
                                <p class="mb0">¿Olvidaste tu contraseña?
                                    <a href="<?php echo base_url() ?>recordatorio">Click aquí</a>
                                </p>
                                <p class="mb0">¿No tenés cuenta?
                                    <a href="<?php echo base_url() ?>registrarse">REGISTRATE aquí</a>
                                </p>
                            -->
                            </div>
                        </div>
                    </div>
                    <!--end of row-->
                </div>
                <!--end of container-->
            </section>