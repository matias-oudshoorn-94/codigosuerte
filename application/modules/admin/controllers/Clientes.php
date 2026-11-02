<?php defined('BASEPATH') OR exit('No direct script access allowed');
	
class Clientes extends MY_Controller 
{
	private $dir = 'clientes';
	private $nom = 'Sucursales';
	private $dir2 = 'Sucursales';
	
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
			'localidad' => 'Localidad',
			'domicilio' => 'Dirección',
			'nombre' => 'Encargado',
			'telefono' => 'Teléfono',
			'email' => 'Email',
			'activo' => 'Estado',	
			'caja_cerrada' => 'Caja diaria'	
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
						
		$this->template->title($this->data['nombre'].' :: Administración');
		$this->template->build('admin/'.$this->dir.'/edit', $this->data);
	}
		
	function update()
	{
		$this->form_validation->set_rules('nombre', 'Nombre', 'required');
		if ((int) $this->input->post('id') === 0)
		{
			$this->form_validation->set_rules('clave', 'Contraseña', 'required');
		}
		
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
			
			$objeto = array(
				'domicilio' => $this->input->post('domicilio'),
				'localidad' => $this->input->post('localidad'),
				'nombre' => $this->input->post('nombre'),
				'apellido' => $this->input->post('apellido'),
				'usuario' => $this->input->post('usuario'),
				'telefono' => $this->input->post('telefono'),
				'email' => $this->input->post('email'),
				'activo'=> $this->input->post('activo'),
				'caja_cerrada'=> $this->input->post('caja_cerrada'),

			);

			// La clave solo se cambia si se escribio una nueva; se guarda encriptada
			$clave = (string) $this->input->post('clave');
			if ($clave !== '')
			{
				$objeto['clave'] = Clientes_model::encriptar_clave($clave);
			}
						
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

	function sub_index($id)
	{

		$this->data['fields'] = array(
			'fecha' => 'Fecha',
			'codigo' => 'Código',
			'copias' => 'Cantidad de copias',
		);
		$this->data['sub_id'] = $id;
		$results = $this->modelo->getAllList($this->data);
		$this->data['rows'] = $results['rows'];
		
		$this->load->view('admin/'.$this->dir.'/sub_list', $this->data);
	}


	function sub_edit($id)
	{
		
		$this->data['sub_id'] = $this->input->post('sub_id');

		if($id!=0)
		{	
			$sql['id'] = $id;				 			
			$results = $this->modelo->getImg($sql);
			$this->data['row'] = $results['rows'][0];
		}	
				
		$this->load->view('admin/'.$this->dir.'/sub_edit', $this->data);
	}



	function sub_update()
	{
		$id = $this->input->post('id');

		$fecha = explode("/", trim($this->input->post('fecha')));
		
			list($day, $month, $year) = $fecha;

			$unix_timestamp = mktime(0,0,0,$month,$day,$year);

			$fecha= date('Y-m-d', $unix_timestamp);
					
		
		if($id==0)
		{	
			$objeto = array(
				'codigo'=> $this->input->post('codigo'),
				'fecha'=> $fecha,
				'copias'=> $this->input->post('copias'),
				'sub_id' => $this->input->post('sub_id')
			);


			$id = $this->modelo->saveImg($objeto);	
			//echo $this->db->last_query();	
			$this->data['message'] = "<div class='alert alert-block'><a class='close' data-dismiss='alert' href='#'>&times;</a><h4 class='alert-heading'>Info !</h4>Registro Guardado</div>";	
		} 
		else
		{
			$objeto = array(
				'codigo'=> $this->input->post('codigo'),
				'fecha'=> $fecha,
				'copias'=> $this->input->post('copias'),
				'id'=> $this->input->post('id'),
				'sub_id' => $this->input->post('sub_id')
			);		


			$this->modelo->sub_update($objeto);
			//echo $this->db->last_query();
			$this->data['message'] = "<div class='alert alert-block'><a class='close' data-dismiss='alert' href='#'>&times;</a><h4 class='alert-heading'>Info !</h4>Registro Modificado</div>";				
		}
		$this->session->set_flashdata('template_msg', $this->data['message']);
		$this->sub_index($this->input->post('sub_id'));		
	}

	function sub_delete()
	{	
		$this->modelo->deleteImg($this->input->post('sub_ele'), $this->input->post('sub_id'));
		//echo $this->db->last_query();
		$this->data['message'] = "<div class='alert alert-block'><a class='close' data-dismiss='alert' href='#'>&times;</a><h4 class='alert-heading'>Info !</h4>Registro Eliminado</div>";	
		$this->session->set_flashdata('template_msg', $this->data['message']);				
		$this->sub_index($this->input->post('sub_id'));
	}

	/**
	 * Historial de contadores de una impresora (AJAX desde la ficha de la impresora).
	 * Reemplaza a include/historial_impresora.php.
	 */
	function historial_impresora()
	{
		$filas = $this->db->select('fecha, impresiones')
			->where('id_impresora', (string) $this->input->post('impresora'))
			->order_by('fecha', 'ASC')
			->get('detalles_impresoras')
			->result();

		echo '<table class="table table-striped"><thead><tr><th scope="col">Fecha</th><th scope="col">Impresiones agregadas</th></tr></thead><tbody>';
		foreach ($filas as $fila)
		{
			echo '<tr><td>'.html_escape($fila->fecha).'</td><td>'.(int) $fila->impresiones.'</td></tr>';
		}
		echo '</tbody></table>';
	}
}
