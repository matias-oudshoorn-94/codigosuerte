<script type="text/javascript">
$(document).ready(function()
{
	$('#contact-form').validate(
	{
		rules: {
			first_name: {
				minlength: 2,
				required: true
			},			
			username: {
				minlength: 2,
				required: true
			},
			idgroup: {
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
	
	$('#acep_pass').click(function()
	{
		$id = $('#id').val();
		$password = $('#new').val();
		$c_password = $('#new_confirm').val();
		
		var request = $.ajax({
			url: "<?php echo site_url('admin/'.$dir.'/change_password/')?>",
			type: "POST",
			data: {id : $id, password : $password, c_password : $c_password},
			dataType: "html"
		});

		request.done(function(msg) {
			$("#pass").html( msg );
		});

		request.fail(function(jqXHR, textStatus) {
			alert( "Request failed: " + textStatus );
		});
		
		return false;
	});	
	
	$('.cancel').click(function()
	{
		$(window.location).attr('href', '<?php echo site_url('admin/'.$dir.'/index');?>');
		$('#form').submit(function() {  
			return false;
		});
	});	
	 
	$('.form').eq (0).find ('input').eq (0).focus ();
	  
}); // end document.ready

</script>	
	<form action="<?php echo $action; ?>" id="form" method="POST" class="form-horizontal" novalidate="novalidate">
		<input type="hidden" id="id" name="id" value="<?php $id = (isset($row->id))?$row->id:"0"; if (isset($_POST['id']) && $_POST['id'] != ''){$id = $_POST['id'];} echo $id;?>"/>
		<div id="page-title" class="clearfix">
			<span style="font-size:24px;font-weight:bold;float:left;margin-top:5px;"><?php echo humanize($dir);?></span>
			<div class="item-content" style="float:right;">							
				<button type="submit" class="btn btn-inverse">Guardar</button>
				<button class="btn cancel">Cancelar</button>
			</div>
		</div> <!-- /.page-title -->
		<div class="row">
			<div class="span12">
				<div id="validation" class="widget highlight widget-form">
					<?php //if(isset($message)) echo $message;?>			
					<div class="widget-content">
						<fieldset>
							<div class="control-group">
								<label class="control-label" for="first_name">Nombre</label>
								<div class="controls">
									<input type="text" class="input-large" name="first_name" id="first_name" value="<?php  if(isset($row->first_name)) echo $row->first_name;?>">
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="username">Usuario</label>
								<div class="controls">
									<input type="text" class="input-large" name="username" id="username" value="<?php  if(isset($row->username)) echo $row->username;?>">
								</div>
							</div>
                            
                            <?php if ($id != 1) { ?> 
                            
								<div class="control-group">
						            <label class="control-label" for="grupo">Tipo de usuario</label>
						            <?php $idgroup = (isset($row->idgroup))?$row->idgroup:FALSE; ?>
						            <div class="controls">
						                <select id="idgroup" name="idgroup">
						                	<?php
							              	if(!isset($row->idgroup)){ ?>
							              		<option value="">Seleccione</option>
							              	<?php } ?>	
						              	<?php 						              		
						              		if($groups)
						              		{
						              			foreach ($groups['rows'] as $group) 
						              			{ $val = ($idgroup==$group->id)?TRUE:'';?>
						              				<option value="<?php echo $group->id?>" <?php echo set_select('idgroup', $idgroup, $val); ?> ><?php echo $group->description?></option>													
												<?php	
						              			}	
						              		}
						              		?>						              				
						              </select>
						            </div>
						        </div>                                              
                                                   <?php } ?>                 
                            
                            
                            <div class="control-group">									
                                <label class="control-label" for="nombre">Activo</label>									
                                <div class="controls"><input name="active" type="checkbox" value="1" <?php if((isset($row->active) && $row->active != 0) || !isset($row->active)) {echo "checked";}?> />
                                </div>
                            </div>	

							<div class="control-group">
								<div class="span4" style="margin-left:80px;">
									<div class="widget widget-accordion">
										<div class="widget-content">
											<div class="accordion" id="sample-accordion">
												<div class="accordion-group">
												  <div class="accordion-heading">
													<a class="accordion-toggle" data-toggle="collapse" data-parent="#sample-accordion" href="#collapseOne">
													Cambiar passwod
													</a>													
													<i class="icon-plus toggle-icon"></i>
												  </div>
												  <div id="collapseOne" class="accordion-body collapse">
													<div class="accordion-inner" >
														<div id="pass"></div>
														<div class="control-group">
															<label class="control-label" for="new">Password</label>
															<div class="controls">
																<input type="password" class="input-medium" name="new" id="new" value="">
															</div>
														</div>
														<div class="control-group">
															<label class="control-label" for="new_confirm">Confirmar Password</label>
															<div class="controls">
																<input type="password" class="input-medium" name="new_confirm" id="new_confirm" value="">
															</div>
														</div>
														<div class="control-group">
															<button id="acep_pass" class="btn btn-inverse btn-small" style="float:right;">Aceptar</button>
														</div>
													</div>
												  </div>
												</div>												
											  </div>												
										</div> <!-- /.widget-content -->										
									</div> <!-- /.widget -->
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