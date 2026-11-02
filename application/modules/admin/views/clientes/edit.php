<script type="text/javascript">
$(document).ready(function()
{
	$('#form').validate(
	{
		rules: {
			nombre: {
				minlength: 2,
				required: true
			},
			apellido: {
				required: true
			},
			email: {
				required: true
			},
			direccion: {
				required: true
			},
			localidad: {
				required: true
			},
			telefono: {
				required: true
			},
			usuario: {
				required: true
			}
	    },
	    focusCleanup: false,
	    
	    highlight: function(label) {
	    	$(label).closest('.control-group').removeClass ('success').addClass('error');
	    },
	    success: function(label) {
	    	label
	    		.text('OK!').addClass('valid')
	    		.closest('.control-group').addClass('success');
		},
		errorPlacement: function(error, element) {
			error.appendTo( element.parents ('.controls') );
		}
	});
	<?php $this->load->view('partials/script_edit.php'); ?>
}); // end document.ready

</script>	


<!-- CKEDITOR-->	
<script src="<?php echo site_url('assets');?>/ckeditor/ckeditor.js"></script>
<script src="<?php echo site_url('assets');?>/ckeditor/samples/js/sample.js"></script>
<link rel="stylesheet" href="<?php echo site_url('assets');?>/ckeditor/samples/css/samples.css">
<link rel="stylesheet" href="<?php echo site_url('assets');?>/ckeditor/samples/toolbarconfigurator/lib/codemirror/neo.css">


	<form action="<?php echo $action?>" id="form" method="POST" enctype="multipart/form-data" class="form-horizontal" novalidate="novalidate">
		<input type="hidden" id="id" name="id" value="<?php echo (isset($row->idusuario))?$row->idusuario:"0"; ?>"/>
		<div id="page-title" class="clearfix">
			<span style="font-size:24px;font-weight:bold;float:left;margin-top:5px;"><?php echo humanize($dir2);?></span>
			<div class="item-content" style="float:right;">							
				<button type="submit" class="btn btn-inverse">Guardar</button>
				<button class="btn cancel">Cancelar</button>
			</div>
		</div> <!-- /.page-title -->
		<div class="row">
			<div class="span12">
				<div id="validation" class="widget highlight widget-form">
								
					<div class="widget-content">
						
							<fieldset>
								<?php 
									
								echo (isset($message))?$message:'';?>

								<div class="control-group">
									<label class="control-label" for="localidad">Localidad</label>
									<div class="controls">
										<?php $localidad = (isset($row->localidad))?$row->localidad:''; ?>
										<input type="text" class="input-xxlarge" name="localidad" id="localidad" value="<?php  echo set_value('localidad', $localidad); ?>">
									</div>
								</div>

								<div class="control-group">
									<label class="control-label" for="domicilio">Domicilio</label>
									<div class="controls">
										<?php $domicilio = (isset($row->domicilio))?$row->domicilio:''; ?>
										<input type="text" class="input-xxlarge" name="domicilio" id="domicilio" value="<?php  echo set_value('domicilio', $domicilio); ?>">
									</div>
								</div>

								<div class="control-group">
									<label class="control-label" for="nombre">Nombre de encargado</label>
									<div class="controls">
										<?php $nombre = (isset($row->nombre))?$row->nombre:''; ?>
										<input type="text" class="input-xxlarge" name="nombre" id="nombre" value="<?php  echo set_value('nombre', $nombre); ?>">
									</div>
								</div>

								<div class="control-group">
									<label class="control-label" for="apellido">Apellido de encargado</label>
									<div class="controls">
										<?php $apellido = (isset($row->apellido))?$row->apellido:''; ?>
										<input type="text" class="input-xxlarge" name="apellido" id="apellido" value="<?php  echo set_value('apellido', $apellido); ?>">
									</div>
								</div>

								<div class="control-group">
									<label class="control-label" for="email">Email</label>
									<div class="controls">
										<?php $email = (isset($row->email))?$row->email:''; ?>
										<input type="text" class="input-xxlarge" name="email" id="email" value="<?php  echo set_value('email', $email); ?>">
									</div>
								</div>

								
								<div class="control-group">
									<label class="control-label" for="nombre">Teléfono</label>
									<div class="controls">
										<?php $telefono = (isset($row->telefono))?$row->telefono:''; ?>
										<input type="text" class="input-xxlarge" name="telefono" id="telefono" value="<?php  echo set_value('telefono', $telefono); ?>">
									</div>
								</div>


								<div class="control-group">
									<label class="control-label" for="usuario">Usuario</label>
									<div class="controls">
										<?php $usuario = (isset($row->usuario))?$row->usuario:''; ?>
										<input  type="text" class="input-xxlarge" name="usuario" id="usuario" value="<?php  echo set_value('usuario', $usuario); ?>">
									</div>
								</div>

								<div class="control-group">
									<label class="control-label" for="clave">Contraseña</label>
									<div class="controls">
										<input type="password" class="input-xxlarge" name="clave" id="clave" value="" autocomplete="new-password">
										<?php if (isset($row->idusuario)): ?>
										<span class="help-block">Dejar vacío para no cambiarla. Las contraseñas se guardan encriptadas y no se pueden ver.</span>
										<?php endif; ?>
									</div>
								</div>

								<div class="control-group">
						            <label class="control-label">Activo</label>
						            <div class="controls">
						              <?php $activo = (isset($row->activo))?$row->activo:1; 
						            	$si=($activo == 1)?TRUE:FALSE;	
						            	$no=($activo == 2)?TRUE:FALSE;

						            ?>
						              <label class="radio">
						                <input type="radio" name="activo" id="si" value="1" <?php echo set_radio('activo', '1', $si); ?>>
						                	Si
						              </label>
						              <label class="radio">
						                <input type="radio" name="activo" id="no" value="2" <?php echo set_radio('activo', '2', $no); ?>>
						                 	No
						              </label>	
						            <?php

						            ?>					             
						            </div>
						        </div>	

						        <div class="control-group">
						            <label class="control-label">Caja del dia cerrada</label>
						            <div class="controls">
						              <?php $caja_cerrada = (isset($row->caja_cerrada))?$row->caja_cerrada:2; 
						            	$si=($caja_cerrada == 1)?TRUE:FALSE;	
						            	$no=($caja_cerrada == 2)?TRUE:FALSE;

						            ?>
						              <label class="radio">
						                <input type="radio" name="caja_cerrada" id="si" value="1" <?php echo set_radio('caja_cerrada', '1', $si); ?>>
						                	Si
						              </label>
						              <label class="radio">
						                <input type="radio" name="caja_cerrada" id="no" value="2" <?php echo set_radio('caja_cerrada', '2', $no); ?>>
						                 	No
						              </label>	
						            <?php

						            ?>					             
						            </div>
						        </div>


								<div class="form-actions">
									<button type="submit" class="btn btn-inverse">Guardar</button>
									<button class="btn cancel" >Cancelar</button>
								</div>
							</fieldset>
						
					</div> <!-- /widget-content -->
				</div>
			</div>
		</div>	
	</form>

		<?php								
	if(isset($row->idusuario)): ?>
		<div id="pedidos"></div>
		<script type="text/javascript">
			$(document).ready(function() {
				$( "#pedidos" ).load( "<?php echo base_url('admin/clientes/sub_index/'.$row->idusuario)?>" );				
			});
		</script>
	<?php
	endif;
	?>
