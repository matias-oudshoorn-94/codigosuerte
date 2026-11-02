
<script type="text/javascript">
$(document).ready(function()
{		
	$('#sub_form').validate(
	{
		rules: {
			copias: {
				required: true
			},	
			codigo: {
				required: true
			},
			fecha: {
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
	$('.cancel').click(function()
	{
		$( "#pedidos" ).load( "<?php echo base_url('admin/'.$dir.'/sub_index/'.$sub_id)?>" );						
	});	
	$('#eliminar').click(function()
	{
		if (!confirm('Eliminar imagen?')) {
            return false
        }
	});
	$('.form').eq (0).find ('input').eq (0).focus ();
	$("form#sub_form").submit(function(event)
	{
		if($(this).valid())
		{	
	  		event.preventDefault();
	 		//grab all form data  
	  		var formData = new FormData($(this)[0]);
	 		
	  		$.ajax({
	    		url: "<?php echo base_url('admin/'.$dir.'/sub_update')?>",
			    type: 'POST',
			    data: formData,
			    async: false,
			    cache: false,
			    contentType: false,
			    processData: false,
			    success: function (returndata) {
			     $( "#pedidos" ).html(returndata);
			    }
			});
			 
		  	return false;
		 } 	
	});

	$('.form').eq(0).find('input').eq(0).focus();
        $('#fecha').datepicker();

	
}); // end document.ready


function historial_impresora($codigo_impresora)
{
	 var impresora = $codigo_impresora;

          $.ajax({
              type: "POST",
              url: '<?php echo site_url('admin/clientes/historial_impresora') ?>', 
              data: {impresora: impresora},
              success: function (data) {
              $("#resultado_historial").html(data);

              }
          });
}


</script>	
	<form action="" id="sub_form" method="POST" enctype="multipart/form-data" class="form-horizontal" novalidate="novalidate">
		<input type="hidden" id="id" name="id" value="<?php echo (isset($row->id))?$row->id:"0"; ?>"/>
		<input type="hidden" id="sub_id" name="sub_id" value="<?php echo $sub_id?>"/>

		<div id="page-title" class="clearfix">
			<span style="font-size:24px;font-weight:bold;float:left;margin-top:5px;">Impresoras</span>
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
                                $fecha_vacia = date("01/01/2021");
                                ?>

		                        <div class="control-group">
		                            <label class="control-label" for="fecha">Fecha <font style="color: red;">*</font></label>
		                            <div class="controls">
		                                <?php $fecha = (isset($row->fecha)) ? $row->fecha : $fecha_vacia; ?>
		                                <input type="text" class="input-xxlarge" name="fecha" id="fecha"
		                                       value="<?php echo set_value('fecha', date("d/m/Y", strtotime($fecha))); ?>">
		                            </div>
		                        </div>   
										
								

				                              


								<div class="control-group">
		                            <label class="control-label" for="codigo">Código</label>
		                            <?php $codigo = (isset($row->codigo)) ? $row->codigo : ''; ?>
		                            <div class="controls">
		                            	<input type="text" class="input-xxlarge" name="codigo" id="codigo" value="<?php  echo set_value('codigo', $codigo); ?>">
		                            </div>
		                        </div>


								<div class="control-group">
									<label class="control-label" for="copias">Cantidad de copias</label>
									<div class="controls">
										<?php $copias = (isset($row->copias))?$row->copias:''; ?>
										<input type="text" class="input-xxlarge" name="copias" id="copias" value="<?php  echo set_value('copias', $copias); ?>">
									</div>
								</div>

								<div class="control-group">
									<label class="btn btn-inverse" for="historial" id="historial" onclick="javascript: historial_impresora('<?php echo $row->codigo?>')">Historial de impresiones</label>
									<div id="resultado_historial">						
									</div>

								</div>

								
								<div class="form-actions">
									<button type="submit" id="sub_guardar" class="btn btn-inverse">Guardar</button>
									<button class="btn cancel" >Cancelar</button>
								</div>
							</fieldset>
						
					</div> <!-- /widget-content -->
				</div>
			</div>
		</div>	
	</form>