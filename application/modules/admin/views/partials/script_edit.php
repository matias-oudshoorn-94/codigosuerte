$('.cancel').click(function()
	{
		$(window.location).attr('href', '<?php echo site_url('admin/'.$dir.'/index');?>');
		$('#form').submit(function() {  
			return false;
		});
	});	
	
	$('.form').eq (0).find ('input').eq (0).focus ();