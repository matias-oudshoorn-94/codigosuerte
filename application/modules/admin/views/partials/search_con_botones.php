<form action="<?php echo base_url('admin/'.$dir.'/index/');?>" id="search-form" class="form-horizontal" method="POST">
	<div class="span6">
		<div class="pull-right">

			<a href="#" data-toggle="modal" class="btn btn-inverse">Imprimir listado<i class="fa fa-print" style="margin-left: 10px"></i></a>

			<a href="#" data-toggle="modal" class="btn btn-inverse" style="margin-right: 10px" onclick="exportar()">Exportar a Excel<i class="fa fa-file-excel-o" style="margin-left: 10px"></i></a>

			<div class="input-append">
				<input class="span2" id="search" name="search" value="<?php if(isset($search)) echo $search;?>" size="16" type="text"><button class="btn" type="submit">Buscar</button>
			</div>
		
		</div>								
	</div>
</form>	


<script>
	function exportar()
	{
       window.open('https://pdfcolor.com.ar/exportar_libros.php','_blank');  
	}
</script>