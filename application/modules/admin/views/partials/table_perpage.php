<div class="span6">
	<div id="r_pag" class="dataTables_length">
		<label>
			<select size="1" name="r_pag" id="per_page" >
				<option value="10" <?php if($this->session->userdata($dir.'_per_page')==10) echo "selected";?>>10</option>
				<option value="25" <?php if($this->session->userdata($dir.'_per_page')==25) echo "selected";?>>25</option>
				<option value="50" <?php if($this->session->userdata($dir.'_per_page')==50) echo "selected";?>>50</option>
				<option value="100" <?php if($this->session->userdata($dir.'_per_page')==100) echo "selected";?>>100</option>
				<option value="500" <?php if($this->session->userdata($dir.'_per_page')==500) echo "selected";?>>500</option>
			</select> 
			Resultado por página
		</label>
	</div>
</div>