<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<title><?php echo $template['title'];?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<link rel="shortcut icon" type="image/x-icon" href="<?php echo base_url(AADMINPATH.'img/candado_a/favicon.ico')?>" >
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

		<!-- Styles -->
		<link href="<?php echo base_url(AADMINPATH.'css/bootstrap.css')?>" rel="stylesheet">
		<link href="<?php echo base_url(AADMINPATH.'css/bootstrap-responsive.css')?>" rel="stylesheet">
		<link href="<?php echo base_url(AADMINPATH.'css/bootstrap-overrides.css')?>" rel="stylesheet">
		<link href="<?php echo base_url(AADMINPATH.'css/components/gallery.css')?>" rel="stylesheet">
		<link href="<?php echo base_url(AADMINPATH.'js/plugins/lightbox/themes/evolution-dark/jquery.lightbox.css')?>" rel="stylesheet"> 
		
		<link href="<?php echo base_url(AADMINPATH.'css/ui-lightness/jquery-ui-1.8.21.custom.css')?>" rel="stylesheet">
		<link href="<?php echo base_url(AADMINPATH.'js/plugins/datatables/DT_bootstrap.css')?>" rel="stylesheet">
		<link href="<?php echo base_url(AADMINPATH.'js/plugins/responsive-tables/responsive-tables.css')?>" rel="stylesheet"> 

		<link href="<?php echo base_url(AADMINPATH.'css/slate.css')?>" rel="stylesheet">
		<link href="<?php echo base_url(AADMINPATH.'css/slate-responsive.css')?>" rel="stylesheet">
				
		<!-- Javascript -->
		<script src="<?php echo base_url(AADMINPATH.'js/jquery-1.7.2.min.js')?>"></script>
		<script src="<?php echo base_url(AADMINPATH.'js/jquery-ui-1.8.21.custom.min.js')?>"></script>
		<script src="<?php echo base_url(AADMINPATH.'js/jquery.ui.touch-punch.min.js')?>"></script>
		<script src="<?php echo base_url(AADMINPATH.'js/bootstrap.js')?>"></script>
		<script src="<?php echo base_url(AADMINPATH.'js/jquery.ui.datepicker-es.js')?>"></script>
		<script src="<?php echo base_url(AADMINPATH.'js/plugins/lightbox/jquery.lightbox.min.js')?>"></script>
	
		<script charset="utf-8" src="<?php echo base_url(AADMINPATH.'js/plugins/validate/jquery.validate.js')?>"></script>
		<script charset="utf-8" src="<?php echo base_url(AADMINPATH.'js/plugins/validate/messages_es.js')?>"></script>

		<script src="<?php echo base_url(AADMINPATH.'js/Slate.js')?>"></script>
		<script type="text/javascript">
		$(document).ready(function()
		{
			<?php $this->load->view('partials/script_list.php'); ?>
		});
		</script>
		
		<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
		<!--[if lt IE 9]>
		<script src="../../../html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->		
	</head>

	<body>
		<div id="header">
			<div class="container">			
				<h1><a href="<?php echo base_url('admin');?>">
				<img src="<?php echo base_url(AADMINPATH.'img/logoblanco.png')?>" alt="<?php echo $nombre;?>" style="padding-top: 15px;padding-left: 15px;width: 85px;">
				</a></h1>			
				<div id="info">				
					<a href="javascript:;" id="info-trigger">
						<i class="icon-cog"></i>
					</a>
					<div id="info-menu">
						<div class="info-details">
							<?php $user_reg = $this->session->userdata('user_reg'); ?>
							<h4>Bienvenido <?php echo $user_reg['first_name'].' '.$user_reg['last_name'];?></h4>
							<p>
								Logueado como <?php echo $user_reg['group']['name'];?>
								<br>
								<a href="<?php echo site_url('admin/logout');?>">Cerrar sesión</a>
							</p>
						</div> <!-- /.info-details -->
					</div> <!-- /#info-menu -->
				</div> <!-- /#info -->
			</div> <!-- /.container -->

		</div> <!-- /#header -->

		 <?php echo $template['partials']['menu']; ?>


		<div id="content">
			
			<div class="container">
				<?php echo $template['body'];?>

		</div> <!-- /#content -->

		<div id="footer">	
			<div class="container">
      	&copy; Derechos reservados  <a href="<?php echo $des_url;?>" title="<?php echo $des_title;?>" alt="<?php echo $des_alt;?>" target="_blank"><?php echo $des_title;?></a> 
			</div> <!-- /.container -->		
		</div> <!-- /#footer -->
	</body>
</html>