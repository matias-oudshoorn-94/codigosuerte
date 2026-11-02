		<?php if ($pages > 1) {?>
        <div class="bullets">
	        <span class="pages">
                <i class="fa fa-angle-left"></i>
                <?php 
				$n = 1;
                for($i=0; $i<$pages; $i++) {
					if ($search == '') {
                 ?>
                &nbsp;<a href="<?php echo base_url().$url."/".$i; ?>" <?php if ($i == $page) {?> class="active" <?php }?>><?php echo $i+1 ?></a>
                <?php 
					}
					else
					{
                 ?>
                &nbsp;<a href="<?php echo base_url().$url."/".$i."/".$search; ?>" <?php if ($i == $page) {?> class="active" <?php }?>><?php echo $i+1 ?></a>
                <?php 
					}	
					$n++;				
					if ($n >18) {echo "&nbsp;<br>"; $n=1;}					
				}?>
                &nbsp;<i class="fa fa-angle-right"></i>
			</span>
        </div><?php }?>    