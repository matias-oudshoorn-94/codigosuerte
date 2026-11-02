<?php
class imagenes_model extends CI_Model
{
	function __construct()
	{ 
		parent::__construct();
	}
	
	function do_upload($config, $archivo='userfile')
	{ 
 		if(!is_dir($config['upload_path']))
		{
			@mkdir($config['upload_path'], 0777, true); //lo creamos
			@chmod($config['upload_path'], 0777); //damos permisos
		}		
		//$this->load->library('upload', $config);
		$this->upload->initialize($config);
		if ( ! $this->upload->do_upload($archivo))
		{
			$error = "<div class='alert alert-block'><a class='close' data-dismiss='alert' href='#'>&times;</a><h4 class='alert-heading'>Error !</h4>".$this->upload->display_errors()."</div>"; 
			$this->session->set_flashdata('template_msg', $error);
		
			return FALSE;
		}
		else
		{
        	$upload_data = $this->upload->data();

        	foreach($config['carpeta'] as $key => $valor)
			{
				if(!is_dir($valor['carpeta']))
				{
					@mkdir($valor['carpeta'], 0777, true); //lo creamos
					@chmod($valor['carpeta'], 0777); //damos permisos
				}

				$image_config["image_library"] = "gd2";
				$image_config["source_image"] = $upload_data["full_path"];
				$image_config['create_thumb'] = FALSE;
				$image_config['maintain_ratio'] = TRUE;
				$image_config['new_image'] = $valor['carpeta'];
				$image_config['quality'] = "90%";
				$image_config['width'] =  $valor['width'];
				$image_config['height'] =  $valor['height'];
				$dim = (intval($upload_data["image_width"]) / intval($upload_data["image_height"])) - ($image_config['width'] / $image_config['height']);
				$image_config['master_dim'] = ($dim > 0)? "height" : "width";
	 
				$this->load->library('image_lib');
				$this->image_lib->initialize($image_config);
					 			
	 			if(!$this->image_lib->resize())
	 			{ //Resize image
	    			$error = "<div class='alert alert-block'><a class='close' data-dismiss='alert' href='#'>&times;</a><h4 class='alert-heading'>Error !</h4>".$this->image_lib->display_errors()."</div>"; 
					$this->session->set_flashdata('template_msg', $error);
					return FALSE;
				}/*
				else if($valor['width'] != 53)
				{
					$image_config['image_library'] = 'gd2';
					$image_config["source_image"] = $upload_data["full_path"];
					$image_config['new_image'] = $valor['carpeta'];
					$image_config['quality'] = "90%";
					$image_config['maintain_ratio'] = FALSE;

					$image_config['width'] =  $valor['width'];
					$image_config['height'] =  $valor['height'];
										
					$image_config['x_axis'] = (($upload_data["image_width"] / 2) - ($image_config['width'] / 2));
					$image_config['y_axis'] = (($upload_data["image_height"] / 2) - ($image_config['height'] / 2));

					
					$this->image_lib->clear();
					$this->image_lib->initialize($image_config); 
	 
					if (!$this->image_lib->crop())
						{ //Cropping
	        			$error = "<div class='alert alert-block'><a class='close' data-dismiss='alert' href='#'>&times;</a><h4 class='alert-heading'>Error !</h4>".$this->image_lib->display_errors()."</div>"; 
	        			echo $error;
						$this->session->set_flashdata('template_msg', $error);
					return FALSE;
					}
				}*/
			}
			return $upload_data['file_name'];
		}

	}
	
	function principal($tipo, $id, $idproducto)
	{
		$this->db->where('idproducto', $idproducto);
		$this->db->where('tipo', $tipo);
		$this->db->set('principal', '0'); 			
		$this->db->update('proximg');
		
		$this->db->where('idimagen', $id);
		$this->db->where('tipo', $tipo);
		$this->db->set('principal', '1'); 			
		$this->db->update('proximg');
	}	
}
?>