<div class="row">
	<div class="span6">
		<div class="dataTables_info" id="example_info">
			<?php  echo ($cur_page>0)?"Página $cur_page de $pagination_tot":"Página $pagination_tot"; ?>									
		</div>
	</div>
	<div class="span6">
		<?php if (strlen($pagination)): 
			echo $pagination; 
		endif; ?>
	</div>
</div>