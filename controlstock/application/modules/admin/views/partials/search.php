<form action="<?php echo base_url('admin/'.$dir.'/index/');?>" id="search-form" class="form-horizontal" method="POST">
	<div class="span6">
		<div class="pull-right">
			<div class="input-append">
				<input class="span2" id="search" name="search" value="<?php if(isset($search)) echo $search;?>" size="16" type="text"><button class="btn" type="submit">Buscar</button>
			</div>
		</div>								
	</div>
</form>	