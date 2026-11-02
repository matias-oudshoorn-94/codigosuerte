<?php defined('BASEPATH') OR exit('No direct script access allowed');
	
class Usuarios extends MY_Controller
{
	private $dir='usuarios';
	private $dir2='usuarios';
	var $data;
	function __construct()
	{
		$user_reg = $this->session->userdata('user_reg'); 
		if ($user_reg['idgroup'] != 1 && $user_reg['idgroup'] != 0) { exit(); }
		
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
	
	// Create
	function add()
	{	    
	
		$this->load->model('groups_model','',TRUE); 
		$this->data['groups'] = $this->groups_model->getAll();	
			
	    // Validate form
		$this->form_validation->set_rules('first_name', 'Nombre', 'required|xss_clean');
		$this->form_validation->set_rules('username', 'Usuario', 'required|xss_clean');	
		$this->form_validation->set_rules('password', 'Contraseña', 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');

		$this->form_validation->set_rules('password_confirm', 'confirme Contraseñaa', 'required');
		$this->form_validation->set_rules('active', 'Activo');		
	   	    
	    if($this->form_validation->run())
	    {  // Validation passes
	       
			$username = strtolower($this->input->post('username'));
			$email    = $username.'@admin.com';
			$password = $this->input->post('password');
			$group = array('1');
			$additional_data = array(
				'first_name' => $this->input->post('first_name'),
				'idgroup' => $this->input->post('idgroup')
			);
		}
		if ($this->form_validation->run() == true && $this->ion_auth->register($username, $password, $email, $additional_data, $group))
		{ 
			//check to see if we are creating the user
			//redirect them back to the admin page
			$this->data['message'] = "<div class='alert alert-block'><a class='close' data-dismiss='alert' href='#'>&times;</a><h4 class='alert-heading'>Info !</h4>Usuario Guardado</div>";	
			$this->session->set_flashdata('template_msg', $this->data['message']);
			$this->index();
		}
		else
		{
			$error = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
			if($error!="")
			{
				$this->data['message'] = "<div class='alert alert-block'><a class='close' data-dismiss='alert' href='#'>&times;</a><h4 class='alert-heading'>Info !</h4>Usuario Guardado: ".$error."</div>";	
				$this->session->set_flashdata('template_msg', $this->data['message']);
			}	
			$this->data['action'] = site_url('admin/'.$this->dir.'/add');
			
			$this->template->title($this->data['nombre'], 'Administración');
			$this->template->build('admin/'.$this->dir.'/add', $this->data);
		}	    
	}
	
    // Retrieve
	function index($sort_by = 'first_name', $sort_order = 'desc', $offset = 0)
	{		   
        $this->load->library('pagination');
        $this->load->library('table'); 
		
		$this->data['fields'] = array(
			'first_name' => 'Nombre',
			'username' => 'Usuario',
			'active' => 'Activo'						
		);
		//- limit -
		if($this->uri->segment(7)) 
			$this->session->set_userdata('per_page', $this->uri->segment(7));
		else if($this->input->post('bnombre')) 
			$this->session->set_userdata('per_page', ""); 
		//--
		if($this->session->userdata('per_page'))	
			$sql['limit'] = $this->session->userdata('per_page');
		else	
			$sql['limit'] = 10;
		//--
		//-	sort_order -
		if($this->uri->segment(5))
		{
			$this->session->set_userdata('sort_order', $this->uri->segment(5));
			$sql['sort_order'] = $this->session->userdata('sort_order');
		}	
		else 
		{
			if($this->session->userdata('sort_order'))	
				$sql['sort_order'] = $this->session->userdata('sort_order');	
			else
			{
				$this->session->set_userdata('sort_order', $sort_order);
				$sql['sort_order'] = $this->session->userdata('sort_order');
			}	
		}	
		//--
		//-	sort_by -	
		if($this->uri->segment(4))
		{
			$this->session->set_userdata('sort_by', $this->uri->segment(4));
			$sql['sort_by'] = $this->session->userdata('sort_by');
		}	
		else 
		{
			if($this->session->userdata('sort_by'))	
				$sql['sort_by'] = $this->session->userdata('sort_by');	
			else
			{
				$this->session->set_userdata('sort_by', $sort_by);
				$sql['sort_by'] = $this->session->userdata('sort_by');
			}	
		}			
		//--	
		$sql['offset'] = ($offset>1)?$offset:NULL;
		$sql['bnombre'] = $this->input->get('bnombre');
		$sql['id_grupo'] = 1;		
		
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
		
		$this->load->model('groups_model','',TRUE); 
		$this->data['groups'] = $this->groups_model->getAll();	

				
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
		$this->form_validation->set_rules('first_name', 'Nombre', 'required|xss_clean');
		$this->form_validation->set_rules('username', 'Usuario', 'required|xss_clean');	
		$this->form_validation->set_rules('active', 'Activo');		
		
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
						
			if($id==0)
			{	//id, nombre, descripcion, orden, imagen
				$objeto = array(
					'idgroup'=> $this->input->post('idgroup'),
					'first_name'=> $this->input->post('first_name'),					
					'username'=> $this->input->post('username'),
					'active'=> $this->input->post('active')					
				); 
				$id = $this->modelo->save($objeto);
				$this->data['message'] = "<div class='alert alert-block'><a class='close' data-dismiss='alert' href='#'>&times;</a><h4 class='alert-heading'>Info !</h4>Registro Guardado</div>";	
				$this->session->set_flashdata('template_msg', $this->data['message']);
				$this->index();
			} 
			else
			{
				$objeto = array(
					'idgroup'=> $this->input->post('idgroup'),	
					'first_name'=> $this->input->post('first_name'),					
					'username'=> $this->input->post('username'),
					'active'=> $this->input->post('active')				
				);
				$this->modelo->update($id,$objeto);
				//echo $this->db->last_query();
				$this->data['message'] = "<div class='alert alert-block'><a class='close' data-dismiss='alert' href='#'>&times;</a><h4 class='alert-heading'>Info !</h4>Registro Modificado</div>";	
				$this->session->set_flashdata('template_msg', $this->data['message']);
				$this->index();
			}
		}	
	}	
	
	// Delete
	function delete()
	{
		$ids = $this->input->post('ele');
		foreach ($ids as $id) 
		{		
			if($this->ion_auth->delete_user($id))
			{	
				$this->data['message'] = "<div class='alert alert-block'><a class='close' data-dismiss='alert' href='#'>&times;</a><h4 class='alert-heading'>Info !</h4>Usuario Eliminado</div>";	
				$this->session->set_flashdata('template_msg', $this->data['message']);				
			}	
			else
			{
				$this->data['message'] = "<div class='alert alert-block'><a class='close' data-dismiss='alert' href='#'>&times;</a><h4 class='alert-heading'>Error !</h4>Al Eliminar Usuario</div>";	
				$this->session->set_flashdata('template_msg', $this->data['message']);		
			}
		}	
		$this->index();			
	}
	
	//change password
	function change_password()
	{
		$this->form_validation->set_rules('password', 'New Password', 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[c_password]');
		$this->form_validation->set_rules('c_password', 'Confirm New Password', 'required');

		if ($this->form_validation->run() == false)
		{ 
			echo "<div class='alert alert-block'><a class='close' data-dismiss='alert' href='#'>&times;</a><h4 class='alert-heading'>Error !</h4>".validation_errors()."</div>";
		}
		else
		{
			$id = $this->input->post('id');		
			$password = $this->input->post('password');	
			$salt       = $this->ion_auth->salt();
			$objeto['password']   = $this->ion_auth->hash_password($password, $salt);
			$this->modelo->update($id,$objeto);
			echo "<div class='alert alert-block'><a class='close' data-dismiss='alert' href='#'>&times;</a><h4 class='alert-heading'>Info !</h4>Contraseña actualizada</div>";
		}
	}
	
	function _get_csrf_nonce()
	{
		$this->load->helper('string');
		$key   = random_string('alnum', 8);
		$value = random_string('alnum', 20);
		$this->session->set_flashdata('csrfkey', $key);
		$this->session->set_flashdata('csrfvalue', $value);

		return array($key => $value);
	}

	function _valid_csrf_nonce()
	{
		if ($this->input->post($this->session->flashdata('csrfkey')) !== FALSE &&
			$this->input->post($this->session->flashdata('csrfkey')) == $this->session->flashdata('csrfvalue'))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	
}

