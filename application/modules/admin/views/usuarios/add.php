<script type="text/javascript">
$(document).ready(function()
{
	$('#form').validate(
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
			},
							
			password: {
				required: true,
				minlength: 8
			},	   
			password_confirm: {
                required: true,
                minlength: 8,
                equalTo: "#password"
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
		$(window.location).attr('href', '<?php echo site_url('admin/'.$dir.'/index');?>');
		$('#form').submit(function() {  
			return false;
		});
	});	
	
	$('.form').eq (0).find ('input').eq (0).focus ();
	  
}); // end document.ready

</script>	
	<form action="<?php echo $action?>" id="form" method="POST" enctype="multipart/form-data" class="form-horizontal" novalidate="novalidate">
		<input type="hidden" id="id" name="id" value="<?php if(isset($row->id)) echo $row->id; ?>"/>
		<input type="hidden" id="group_id" name="group_id" value="1"/>
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
					<?php if(isset($message)) echo $message;?>			
					<div class="widget-content">
						
						<fieldset>
							<div class="control-group">
								<label class="control-label" for="first_name">Nombre</label>
								<div class="controls">
									<input type="text" class="input-large" name="first_name" id="first_name" value="<?php echo set_value('first_name'); ?>">
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="username">Usuario</label>
								<div class="controls">
									<input type="text" class="input-large" name="username" id="username" value="<?php echo set_value('username'); ?>">
								</div>
							</div>

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
                            
						   <div class="control-group">
								<label class="control-label" for="password">Contraseña</label>
								<div class="controls">
									<input type="password" class="input-large" name="password" id="password" value="<?php  if(isset($row->password)) echo $row->password;?>">
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="password_confirm">Confirme Contraseña</label>
								<div class="controls">
									<input type="password" class="input-large" name="password_confirm" id="password_confirm" value="<?php  if(isset($row->password)) echo $row->password;?>">
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