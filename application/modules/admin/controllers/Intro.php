<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Intro extends MY_Controller
{
	private $dir = 'intro';
	private $nom = 'Intros';
	private $dir2 = 'Introducci&oacuten categor&iacuteas';
	//private $sub_dir = 'Imágenes';

	function __construct()
	{
		parent::__construct();
		if($this->session->userdata('ubicacion')!= $this->dir)
		{
			$array_items = array('per_page' => NULL, 'sort_order' => NULL, 'sort_by' => NULL, );
			$this->session->unset_userdata($array_items);
			$this->session->set_userdata('ubicacion', $this->dir);
		}
		$this->load->model($this->dir.'_model','modelo',TRUE);
		$this->data['dir'] = $this->dir;
		$this->data['dir2'] = $this->dir2;
		$this->data['sub_dir'] = $this->sub_dir;
	}

	function index($sort_by = 'nombre', $sort_order = 'asc', $offset = 0)
	{
		$this->load->helper('text');
        $this->load->library('pagination');
        $this->load->library('table');

		$this->data['fields'] = array(
			'nombre' => 'Nombre',
			'introduccion' => 'Introducción'
		);

		$sql['offset'] = ($offset>1)?$offset:NULL;
		//var_dump($sql);
		$results = $this->modelo->getAll($sql);
		$this->data['rows'] = $results['rows'];
		$this->data['num_results'] = $results['num_rows'];

		$this->template->title('EFECTO SUMA', 'Administración');
		//$this->template->build('admin/'.$this->dir.'/list', $this->data);
		$this->template->build('admin/producto/list', $this->data);


	}

	function edit($id)
	{

		if($id!=0)
		{
			$sql['id'] = $id;
			$results = $this->modelo->getAll($sql);
			$this->data['row'] = $results['rows'][0];
			$this->data['num_results'] = $results['num_rows'];
		}
		$this->data['action'] = site_url('admin/'.$this->dir.'/update');
		$this->template->title('EFECTO SUMA :: Administración');
		$this->template->build('admin/'.$this->dir.'/edit', $this->data);

	}

	function update()
	{
		//$this->form_validation->set_rules('nombre', 'Nombre','required');
		$this->form_validation->set_rules('introduccion', 'Introducción','required');
		//$this->form_validation->set_rules('userfile', 'Imagen','required');

		if ($this->form_validation->run() == FALSE)
		{
			$this->data['action'] = site_url('admin/'.$this->dir.'/update');
			$this->data['message'] = "<div class='alert alert-block'><a class='close' data-dismiss='alert' href='#'>&times;</a><h4 class='alert-heading'>Errores !</h4>".validation_errors()."</div>";
			$this->template->title($this->data['nombre'].' :: Administración');
			$this->template->build('admin/'.$this->dir.'/edit', $this->data);
		}
		else
		{
			$id = $this->input->post('id');
			$config['upload_path'] = 'assets/archivos/'.$this->dir.'/normal/';
			$config['allowed_types'] = 'gif|jpg|jpeg|png';
			$config['max_size']	= '2000';
			$config['carpeta'] = array();
			$config['carpeta'][0]=array(
				'carpeta' => 'assets/archivos/'.$this->dir.'/thumbs/',
				'width' => 53,
				'height' => 53
			);

			$archivo='';
			$error = NULL;

			if (isset($_FILES['userfile']) && $_FILES['userfile']['name']!='')
			{
				//echo "archivo: ".$_FILES['userfile']['name'];

				$this->load->model('imagenes_model','',TRUE);
				$archivo = $this->imagenes_model->do_upload($config);
			}

			if($archivo==FALSE) $this->index();

			
			if($_FILES['userfile']['name']!='')
			{
				$objeto = array
				(
					//'nombre'=> $this->input->post('nombre'),
					'introduccion'=> $this->input->post('introduccion'),
				);
			}

			if($_FILES['userfile']['name']=='')
			{
				$objeto = array
				(
					//'nombre'=> $this->input->post('nombre'),
					'introduccion'=> $this->input->post('introduccion'),
				);
			}

			

			$this->modelo->update($id,$objeto);
			if($id==0)
			{
				if($archivo!='') $objeto['imagen'] = $archivo;
				$id = $this->modelo->save($objeto);
				$this->data['message'] = "<div class='alert alert-block'><a class='close' data-dismiss='alert' href='#'>&times;</a><h4 class='alert-heading'>Info !</h4>Registro Guardado</div>";
				$this->session->set_flashdata('template_msg', $this->data['message']);
				redirect('admin/'.$this->dir.'/index', 'refresh');
			}
			else
			{
				if($archivo!='') $objeto['imagen'] = $archivo;
				$this->modelo->update($id,$objeto);
				//echo $this->db->last_query();
				$this->data['message'] = "<div class='alert alert-block'><a class='close' data-dismiss='alert' href='#'>&times;</a><h4 class='alert-heading'>Info !</h4>Registro Modificado</div>";
				$this->session->set_flashdata('template_msg', $this->data['message']);
				//redirect('admin/'.$this->dir.'/index', 'refresh');
				redirect('admin/producto', 'refresh');
			}
		}
	}

	function delete()
	{
		$this->modelo->delete($this->input->post('ele'));
		$this->data['message'] = "<div class='alert alert-block'><a class='close' data-dismiss='alert' href='#'>&times;</a><h4 class='alert-heading'>Info !</h4>Registro Eliminado</div>";
		$this->session->set_flashdata('template_msg', $this->data['message']);
		redirect('admin/'.$this->dir.'/index', 'refresh');

	}

	function eliminar_img($id)
	{
		$this->modelo->delete_img($id);
		$this->edit($id);
	}

	function sub_index($id)
	{
		$this->data['fields'] = array(
			'nombre' => 'Imagen',
			'orden' => 'Orden',
			'principal' => 'Principal'
		);
		$this->data['sub_id'] = $id;
		$results = $this->modelo->getAllList($this->data);
		$this->data['rows'] = $results['rows'];
		//$this->data['num_results'] = $results['num_rows'];

		$this->load->view('admin/'.$this->dir.'/sub_list', $this->data);
	}


	function sub_edit($id)
	{/*
		$this->data['sub_id'] = $this->input->post('sub_id');

		if($id!=0)
		{
			$sql['id'] = $id;
			$results = $this->modelo->getImg($sql);
			$this->data['row'] = $results['rows'][0];
		}

		$this->load->view('admin/'.$this->dir.'/sub_edit', $this->data);*/
	}


	function sub_update()
	{/*
		$id = $this->input->post('id');
		

		$config['upload_path'] = 'assets/archivos/'.$this->dir.'/original/';
		$config['allowed_types'] = 'gif|jpg|jpeg|png';
		$config['max_size'] = '0';
		$config['max_width']  = '0';
		$config['max_height']  = '0'; 
		$config['carpeta'] = array();
		// $config['carpeta'][0]=array(
		// 	'carpeta' => 'assets/archivos/'.$this->dir.'/normal/',
		// 	'tipo' => 'normal',
		// 	'width' => 460,
		// 	'height' => 460
		// );
		// $config['carpeta'][1]=array(
		// 	'carpeta' => 'assets/archivos/'.$this->dir.'/thumbs/',
		// 	'tipo' => 'thumbs',
		// 	'width' => 160,
		// 	'height' => 120
		// );
		// $config['carpeta'][2]=array(
		// 	'carpeta' => 'assets/archivos/'.$this->dir.'/miniatura/',
		// 	'tipo' => 'miniatura',
		// 	'width' => 258,
		// 	'height' => 258
		// );
			
		$archivo='';
		$error = NULL;
		if (isset($_FILES['userfile']) && $_FILES['userfile']['name']!='') 
		{
			$this->load->model('imagenes_model','',TRUE); 	
			$archivo = $this->imagenes_model->do_upload($config);
		}	

		if($id==0)
		{	//id, nombre, descripcion, orden, imagen			
			$objeto = $this->input->post();
			if($this->input->post('principal')==1) $this->modelo->resetPrincipal($this->input->post('principal'),$this->input->post('sub_id'));
			if($archivo!='') $objeto['nombre'] = $archivo;	
			$id = $this->modelo->saveImg($objeto);		
			$this->data['message'] = "<div class='alert alert-block'><a class='close' data-dismiss='alert' href='#'>&times;</a><h4 class='alert-heading'>Info !</h4>Registro Guardado</div>";	
		} 
		else
		{
			$objeto = $this->input->post();		
			if($this->input->post('principal')==1) $this->modelo->resetPrincipal($this->input->post('principal'),$this->input->post('sub_id'));
			if($archivo!='') $objeto['nombre'] = $archivo;					
			$this->modelo->sub_update($objeto);	
			//echo $this->db->last_query();
			$this->data['message'] = "<div class='alert alert-block'><a class='close' data-dismiss='alert' href='#'>&times;</a><h4 class='alert-heading'>Info !</h4>Registro Modificado</div>";				
		}
		$this->session->set_flashdata('template_msg', $this->data['message']);
		$this->sub_index($this->input->post('sub_id'));		*/
	}

	function sub_delete()
	{	/*
		$this->modelo->deleteImg($this->input->post('sub_ele'), $this->input->post('sub_id'));
		//echo $this->db->last_query();
		$this->data['message'] = "<div class='alert alert-block'><a class='close' data-dismiss='alert' href='#'>&times;</a><h4 class='alert-heading'>Info !</h4>Registro Eliminado</div>";	
		$this->session->set_flashdata('template_msg', $this->data['message']);				
		$this->sub_index($this->input->post('sub_id'));*/
	}
}

