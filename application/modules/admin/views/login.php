<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="utf-8">
		<title>Login</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
		<meta name="apple-mobile-web-app-capable" content="yes">  
		<link rel="shortcut icon" type="image/x-icon" href="<?php echo base_url(AADMINPATH.'img/candado_c/favicon.ico')?>" >
		
		<!-- Styles -->    
		<link href="<?php echo base_url(AADMINPATH.'css/bootstrap.css')?>" rel="stylesheet">
		<link href="<?php echo base_url(AADMINPATH.'css/bootstrap-responsive.css')?>" rel="stylesheet">
		<link href="<?php echo base_url(AADMINPATH.'css/bootstrap-overrides.css')?>" rel="stylesheet">
		<link href="<?php echo base_url(AADMINPATH.'css/ui-lightness/jquery-ui-1.8.21.custom.css')?>" rel="stylesheet">
		<link href="<?php echo base_url(AADMINPATH.'css/slate.css')?>" rel="stylesheet">
		<link href="<?php echo base_url(AADMINPATH.'css/components/signin.css')?>" rel="stylesheet" type="text/css">   
		<!-- Javascript -->    
		<script src="<?php echo base_url(AADMINPATH.'js/jquery-1.7.2.min.js')?>"></script>
		<script src="<?php echo base_url(AADMINPATH.'js/jquery-ui-1.8.18.custom.min.js')?>"></script>    
		<script src="<?php echo base_url(AADMINPATH.'js/jquery.ui.touch-punch.min.js')?>"></script>
		<script src="<?php echo base_url(AADMINPATH.'js/bootstrap.js')?>"></script>
	</head>
	<body>
		
		
		<div class="account-container login">
			<div class="content clearfix">
				<div class="span4" style="margin:auto;">
					
					<img src="<?php echo base_url(AADMINPATH.'img/logo1.png')?>" alt="<?php echo $this->data['nombre'];?>" style="padding-right: 50px;padding-left: 50px;width: 240px;">

				</div>
				<div style="height:20px;clear:both;"></div>
				<?php echo form_open("admin/login");?>
					<h3>Acceso al administrador</h3>		
					<div class="login-fields">
						<p><?php echo (isset($message))?$message:'';?></p>
						<div class="field">
							<label for="identity">Usuario:</label>
							<input type="text" id="identity" name="identity" value="" placeholder="Usuario" class="login username-field" />
						</div> <!-- /field -->
						<div class="field">
							<label for="password">Password:</label>
							<input type="password" id="password" name="password" value="" placeholder="Password" class="login password-field"/>
						</div> <!-- /password -->
					</div> <!-- /login-fields -->
					<div class="login-actions">
						<span class="login-checkbox">
							<input id="Field" name="Field" type="checkbox" class="field login-checkbox" value="First Choice" tabindex="4" />
							<label class="choice" for="Field">Recordar mi identidad </label>
						</span>
						<button class="button btn btn-secondary btn-large">Entrar</button>
					</div> <!-- .actions -->
				<?php echo form_close();?>
			</div> <!-- /content -->
		</div> <!-- /account-container -->
		<!-- Text Under Box -->
		<div class="login-extra">
			Desarrollado por  <a href="<?php echo $this->data['des_url'];?>" title="<?php echo $this->data['des_title'];?>" alt="<?php echo $this->data['des_alt'];?>" target="_blank"><?php echo $this->data['des_title'];?></a> 
		</div> <!-- /login-extra -->
	</body>
</html>