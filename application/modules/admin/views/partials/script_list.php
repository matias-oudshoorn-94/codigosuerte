$("#list .orden").click(function()
{
   $(window.location).attr('href', '<?php echo base_url();?>'+$(this).attr('rel'));
});   

$("#per_page").change(function () {
	$("#per_page option:selected").each(function () {
		$(window.location).attr('href', '<?php echo site_url('admin/'.$dir.'/index/')."/".$this->session->userdata($dir.'_sort_by')."/".$this->session->userdata($dir.'_sort_order')."/1/";?>'+$(this).val());
	});	  
});

$("#per_page2").change(function () {
		$("#per_page2 option:selected, #per_page2 option:selected").each(function () {
			$(window.location).attr('href', '<?php echo site_url('admin/'.$dir.'/index/')."/".$this->session->userdata('sort_by')."/".$this->session->userdata('sort_order')."/1/";?>'+$(this).val());
		});	  
	});		

 
// add multiple select / deselect functionality
$("#all").click(function () {
	$('.ele').attr('checked', this.checked);
});
// if all checkbox are selected, check the selectall checkbox
// and viceversa
$(".ele").click(function(){
	if($(".ele").length == $(".ele:checked").length) {
		$("#all").attr("checked", "checked");
	} else {
		$("#all").removeAttr("checked");
	}
});
$('#nuevo').click(function()
{
	$(window.location).attr('href', '<?php echo site_url('admin/'.$dir.'/edit/0')?>');
});	
$('#eliminar').click(function()
{
	var selectedItems = new Array();
	$("input[@name='ele[]']:checked").each(function(){
		selectedItems.push($(this).val());
	});
	if (selectedItems.length > 0)
	{
		 $('#delete-form').submit();
	}	
	else
	{
		$(".close").click();
	}
});	
$("#breset").click(function()
{
	$(window.location).attr('href', '<?php echo site_url('admin/'.$dir)?>');		
});