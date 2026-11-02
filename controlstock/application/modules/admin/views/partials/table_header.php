<thead>
	<th style="width:30px;text-align:center;">
		<input type="checkbox" name="all" id="all" value="" />
	</th>
	<?php foreach($fields as $field_name => $field_display): ?>
		<th <?php echo ($this->session->userdata($dir.'_sort_by') == $field_name)?"class='orden sorting_".$this->session->userdata($dir.'_sort_order')."'":"class='orden sorting'";?> rel="admin/<?php echo $dir;?>/index/<?php echo $field_name."/";?><?php echo ($this->session->userdata($dir.'_sort_order') == 'asc') ? 'desc' : 'asc'; ?>" tabindex="0" rowspan="1" colspan="1">
			<?php echo 	$field_display; ?>
		</th>
	<?php endforeach; ?>
</thead>