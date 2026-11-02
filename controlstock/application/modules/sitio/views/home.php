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
                                <h4 class="uppercase">BIENVENIDO AL SISTEMA DE PDF COLOR</h4>

                                <a class="btn btn-filled btn-lg" href="<?php echo base_url() ?>login">Ingresar</a>
                                
                            </div>
                        </div>
                    </div>
                    <!--end of row-->
                </div>
                <!--end of container-->
            </section>