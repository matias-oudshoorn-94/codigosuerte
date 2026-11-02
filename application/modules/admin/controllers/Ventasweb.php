<?php defined('BASEPATH') OR exit('No direct script access allowed');
	
class Ventasweb extends MY_Controller 
{
	private $dir = 'ventasweb';
	private $nom = 'Ventas Web';
	private $dir2 = 'Ventas Web';
	
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
	}
	
	function index($sort_by = 'nombre', $sort_order = 'asc', $offset = 0)
	{		   
		$this->load->helper('text');
        $this->load->library('pagination');
        $this->load->library('table'); 
		
		$this->data['fields'] = array(
			'fecha' => 'Fecha',
			'descripcion' => 'Descripción',
			'importe' => 'Importe',
			'nombre' => 'Medio de pago',
		);
		//- search -
		if($this->get_search($this->dir) != '') $sql['search'] = $this->get_search($this->dir);
		//--
		//- limit -		
		$sql['limit'] = $this->get_limit($this->dir);
		//--
		//-	sort_order -
		$sql['sort_order'] = $this->get_sort_order($this->dir, $sort_order);
		//--
		//-	sort_by -	
		$sql['sort_by'] = $this->get_sort_by($this->dir, $sort_by);			
		//--	
		$sql['offset'] = ($offset>1)?$offset:NULL;
		//var_dump($sql);
		$results = $this->modelo->getAll($sql);
				
		$this->data['rows'] = $results['rows'];
		$this->data['num_results'] = $results['num_rows'];
		
		$config = array();
		$config['base_url'] = site_url("admin/".$this->dir."/index/$sort_by/$sort_order");
		$config['uri_segment'] = 5;
		$config['total_rows'] = $this->data['num_results'];
		$config['per_page'] = $sql['limit'];

		if($this->session->flashdata('template_msg'))
			$this->data['message'] =	$this->session->flashdata('template_msg');
		
		$this->pagination->initialize($config); 
		
		$this->data['pagination'] = $this->pagination->create_links();	
		$this->data['cur_page'] = $this->pagination->cur_page;
		$this->data['pagination_tot'] = ceil(($config['total_rows']/$this->pagination->per_page));
		$this->data['action'] = site_url('admin/'.$this->dir.'/index');
		$this->data['search'] = $this->session->userdata($this->dir.'_search');
		//print_r($this->session);
		$this->template->title($this->data['nombre'], 'Administración');
		$this->template->build('admin/'.$this->dir.'/list', $this->data);
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

		$this->load->model('rubro_model','',TRUE); 
		$this->data['rubros'] = $this->rubro_model->getAll();
						
		$this->template->title($this->data['nombre'].' :: Administración');
		$this->template->build('admin/'.$this->dir.'/edit', $this->data);
	}
		
	function update()
	{
		$this->form_validation->set_rules('descripcion', 'Descripcion', 'required');
		
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
			/*----*/
			$config['upload_path'] = 'assets/archivos/'.$this->dir.'/original/';
			$config['allowed_types'] = 'gif|jpg|jpeg|png';
			$config['max_size']	= '2000';
			$config['carpeta'] = array();
			
			$archivo='';
			$error = NULL;
			
			if (isset($_FILES['userfile']) && $_FILES['userfile']['name']!='') 
			{
				$this->load->model('imagenes_model','',TRUE); 	
				$archivo = $this->imagenes_model->do_upload($config);
			}
			if($archivo==FALSE) $this->index();
			/*----*/

			//$fecha = date("Y-m-d");
			
			$objeto = array(
				'descripcion'=> $this->input->post('descripcion'),
				'importe'=> $this->input->post('importe'),
				'idrubro'=> $this->input->post('idrubro')
			);
						
			if($id==0)
			{
				if($archivo!='') $objeto['logo'] = $archivo;	
				$id = $this->modelo->save($objeto);
				$this->data['message'] = "<div class='alert alert-block'><a class='close' data-dismiss='alert' href='#'>&times;</a><h4 class='alert-heading'>Info !</h4>Registro Guardado</div>";	
				$this->session->set_flashdata('template_msg', $this->data['message']);
				redirect('admin/'.$this->dir.'/index', 'refresh');
			} 
			else
			{
				if($archivo!='') $objeto['logo'] = $archivo;	
				$this->modelo->update($id,$objeto);
				//echo $this->db->last_query();
				$this->data['message'] = "<div class='alert alert-block'><a class='close' data-dismiss='alert' href='#'>&times;</a><h4 class='alert-heading'>Info !</h4>Registro Modificado</div>";	
				$this->session->set_flashdata('template_msg', $this->data['message']);
				redirect('admin/'.$this->dir.'/index', 'refresh');
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
}

