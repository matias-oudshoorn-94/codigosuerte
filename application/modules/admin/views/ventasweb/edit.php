<script type="text/javascript">
$(document).ready(function()
{
	$('#form').validate(
	{
		rules: {
			descripcion: {
				minlength: 2,
				required: true
			},
			importe: {
				required: true
			},
			idrubro: {
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
		<input type="hidden" id="id" name="id" value="<?php echo (isset($row->id))?$row->id:"0"; ?>"/>
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
									
								//echo (isset($message))?$message:'';?>
								<div class="control-group">
									<label class="control-label" for="descripcion">Descripción <font style="color: red;">*</font></label>
									<div class="controls">
										<?php $descripcion = (isset($row->descripcion))?$row->descripcion:''; ?>
										<textarea class="input-xxlarge" name="descripcion" id="descripcion" ><?php  echo set_value('descripcion', $descripcion); ?></textarea>
									</div>
								</div>

								<div class="control-group">
									<label class="control-label" for="importe">Importe <font style="color: red;">*</font></label>
									<div class="controls">
										<?php $importe = (isset($row->importe))?$row->importe:''; ?>
										<input type="text" class="input-xxlarge" name="importe" id="importe" value="<?php  echo set_value('importe', $importe); ?>">
									</div>
								</div>

								<div class="control-group">                            
								     <label class="control-label" for="medio">Medios de pago <font style="color: red;">*</font></label>
								            <?php $idrubro = (isset($row->idrubro))?$row->idrubro:FALSE; ?>
									<div class="controls">
			                                    
								                <select id="idrubro" name="idrubro">

									              		<option value="">Seleccione</option>
			
								              	<?php 						              		
								              		if($rubros)
								              		{
								              			foreach ($rubros['rows'] as $rubro) 
								              			{ $val = ($idrubro==$rubro->id)?TRUE:'';?>
								              				<option value="<?php echo $rubro->id?>" <?php echo set_select('idrubro', $idrubro, $val); ?> ><?php echo $rubro->nombre?></option>
														<?php
								              			}	
								              		}
								              		?>						              				
								              </select>
		                          
								            </div>                            
                                </div>

                                <!--

								
								<div class="control-group">
									<label class="control-label" for="orden">Orden</label>
									<div class="controls">
										<?php $orden = (isset($row->orden))?$row->orden:''; ?>
										<input type="text" class="input-xxlarge" name="orden" id="orden" value="<?php  echo set_value('orden', $orden); ?>">
									</div>
								</div>

								-->

								

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