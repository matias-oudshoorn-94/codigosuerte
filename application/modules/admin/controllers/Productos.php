<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Productos extends MY_Controller
{
	private $dir = 'productos';
	private $nom = 'Productos';
	private $dir2 = 'Productos';

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
        'nombre' => 'Nombre',
        'codigo' => 'Código',
        'precio' => 'Precio de costo',
        'porcentaje_marcado' => 'Porcentaje de marcado',
        'precio_final' => 'Precio de venta',
        'stock' => 'Stock total',
        'fecha_vencimiento' => 'Fecha de vencimiento',
    );

    // --- Filtros y paginación ---
    if ($this->get_search($this->dir) != '') $sql['search'] = $this->get_search($this->dir);
    $sql['limit'] = $this->get_limit($this->dir);
    $sql['sort_order'] = $this->get_sort_order($this->dir, $sort_order);
    $sql['sort_by'] = $this->get_sort_by($this->dir, $sort_by);
    $sql['offset'] = ($offset > 1) ? $offset : NULL;

    $results = $this->modelo->getAll($sql);
    $this->data['rows'] = $results['rows'];
    $this->data['num_results'] = $results['num_rows'];

    $config['base_url'] = site_url("admin/" . $this->dir . "/index/$sort_by/$sort_order");
    $config['total_rows'] = $this->data['num_results'];
    $config['per_page'] = $sql['limit'];
    $this->pagination->initialize($config);

    $this->data['pagination'] = $this->pagination->create_links();
    $this->data['cur_page'] = $this->pagination->cur_page;
    $this->data['pagination_tot'] = ceil(($config['total_rows'] / $this->pagination->per_page));

    // --- Cargar modelos ---
    $this->load->model('stockproductos_model', 'stockmodelo', TRUE);
    $this->load->model('clientes_model', 'sucursales', TRUE);

    $this->data['sucursales'] = $this->sucursales->getAll();

    // --- Obtener stock por producto ---
    $stocks_por_producto = [];
    foreach ($results['rows'] as $producto) {
        $stocks_por_producto[$producto->id] = $this->stockmodelo->get_by_producto($producto->id);
    }
    $this->data['stocks_por_producto'] = $stocks_por_producto;

    $this->template->title($this->data['nombre'], 'Administración');
    $this->template->build('admin/' . $this->dir . '/list', $this->data);
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

		$this->load->model('stockproductos_model','stockmodelo',TRUE);
		$this->load->model('clientes_model','sucursales',TRUE); // ahora apunta al modelo correcto
		$this->data['sucursales'] = $this->sucursales->getAll(); // devuelve array de sucursales
		$this->data['stocks'] = $this->stockmodelo->get_by_producto($id); // devuelve array de stocks

		$this->data['action'] = site_url('admin/'.$this->dir.'/update');

		$this->template->title($this->data['nombre'].' :: Administración');
		$this->template->build('admin/'.$this->dir.'/edit', $this->data);
	}


	function update()
	{
		$this->form_validation->set_rules('nombre', 'Nombre', 'required');

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


			$fecha = explode("/", trim($this->input->post('fecha_vencimiento')));

			list($day, $month, $year) = $fecha;

			$unix_timestamp = mktime(0,0,0,$month,$day,$year);

			$fecha= date('Y-m-d', $unix_timestamp);

			if($this->input->post('precio_final') == '') {
				$porcentaje = $this->input->post('precio') * $this->input->post('porcentaje_marcado') / 100;
				$precio_final = $this->input->post('precio') + $porcentaje;
			}
			else {
				$precio_final = $this->input->post('precio_final');
			}



			$objeto = array(
				'nombre'=> $this->input->post('nombre'),
				'codigo'=> $this->input->post('codigo'),
				'precio'=> $this->input->post('precio'),
				'stock'=> $this->input->post('stock'),
				'orden'=> $this->input->post('orden'),
				'precio_final'=> $precio_final,
				'porcentaje_marcado'=> $this->input->post('porcentaje_marcado'),
				'fecha_vencimiento'=> $fecha
			);

			if($id==0)
			{
				$this->load->model('stockproductos_model','stockmodelo',TRUE);
				$sucursales = $this->input->post('stock_sucursal');
				if ($sucursales) {
					foreach ($sucursales as $idusuario => $stock) {
						$this->stockmodelo->save_or_update($id, $idusuario, $stock);
					}
				}
				if($archivo!='') $objeto['logo'] = $archivo;
				$id = $this->modelo->save($objeto);
				$this->data['message'] = "<div class='alert alert-block'><a class='close' data-dismiss='alert' href='#'>&times;</a><h4 class='alert-heading'>Info !</h4>Registro Guardado</div>";
				$this->session->set_flashdata('template_msg', $this->data['message']);
				redirect('admin/'.$this->dir.'/index', 'refresh');
			}
			else
			{
				$this->load->model('stockproductos_model','stockmodelo',TRUE);
				$sucursales = $this->input->post('stock_sucursal');
				if ($sucursales) {
					foreach ($sucursales as $idusuario => $stock) {
						$this->stockmodelo->save_or_update($id, $idusuario, $stock);
					}
				}
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

