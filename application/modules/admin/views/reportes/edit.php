<script type="text/javascript">
$(document).ready(function()
{
	$('#form').validate(
	{
		rules: {
			nombre: {
				minlength: 2,
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
									<label class="control-label" for="nombre">Nombre <font style="color: red;">*</font></label>
									<div class="controls">
										<?php $nombre = (isset($row->nombre))?$row->nombre:''; ?>
										<input type="text" class="input-xxlarge" name="nombre" id="nombre" value="<?php  echo set_value('nombre', $nombre); ?>">
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