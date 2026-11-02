<?php defined('BASEPATH') OR exit('No direct script access allowed');
	
class Productos extends MY_Controller 
{
	private $dir = 'productos';
	private $nom = 'Productos';
	private $dir2 = 'Productos';
	private $sub_dir = 'Imágenes';
	
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
	
	function index($sort_by = 'id', $sort_order = 'desc', $offset = 0)
	{		   

        $this->load->library('pagination');
        $this->load->library('table'); 
		
		$this->data['fields'] = array(
			'id' => 'ID',
			'nombre' => 'Nombre',
			'cantidad' => 'Stock disponible',
			'fecha' => 'Última modificación'

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
		$config['total_rows'] = $this->data['num_results'];
		$config['per_page'] = $sql['limit'];
			
		if($this->session->flashdata('template_msg'))
			$this->data['message'] =	$this->session->flashdata('template_msg');
		
		$this->pagination->initialize($config); 
		$this->data['pagination'] = $this->pagination->create_links();	
		$this->data['cur_page'] = $this->pagination->cur_page;
		$this->data['pagination_tot'] = ceil(($config['total_rows']/$this->pagination->per_page));
		$this->data['action'] = site_url('admin/'.$this->dir.'/index');
		$this->data['bnombre'] = $sql['bnombre'];

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

	
		$this->template->title('PDF Color :: Administración');
		$this->template->build('admin/'.$this->dir.'/edit', $this->data);
	}

	function sluggable($str) {



		$before = array(

			'àáâãäåòóôõöøèéêëðçìíîïùúûüñšž',

			'/[^a-z0-9\s]/',

			array('/\s/', '/--+/', '/---+/')

		);

	 

		$after = array(

			'aaaaaaooooooeeeeeciiiiuuuunsz',

			'',

			'-'

		);

	

		$str = strtolower($str);

		$str = strtr($str, $before[0], $after[0]);

		$str = preg_replace($before[1], $after[1], $str);

		$str = trim($str);

		$str = preg_replace($before[2], $after[2], $str);

	
		return $str;

	}

		
	function update()
	{
		$this->form_validation->set_rules('nombre', 'Nombre','required');
		
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
				$this->load->model('imagenes_model','',TRUE);
				$archivo = $this->imagenes_model->do_upload($config);
			}
			if($archivo==FALSE) $this->index();
			/*----*/

			$fecha = Date('Y-m-d H:i:s');

			$objeto = array(
				'nombre' => $this->input->post('nombre'),
				'cantidad' => $this->input->post('cantidad'),
				'fecha' => $fecha
			);

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

