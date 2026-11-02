<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Sitio extends MX_Controller
{
	var $data;
	var $meta;
	var $sql;

	function __construct()
	{
		parent::__construct();
		$this->template->set_layout('sitio_template');
		$this->template->enable_parser(FALSE);
		$this->load->library('form_validation');

		//Carro
		if (isset($this->session->userdata("user_data")[0]->idusuario))
		{
			$this->load->model('admin/shopping_model','modelo_carro',TRUE);
			$sql['limit'] = NULL;
			$sql['offset'] = NULL;
			/*$sql['where'] = "0";*/
			$sql['sort_order'] = "asc";
			$sql['sort_by'] = "productos.nombre";
			$sql['cod_clie'] = $this->session->userdata("user_data")[0]->idusuario;
			$results = $this->modelo_carro->getAll($sql);
			$this->data['rows_carro'] = $results['rows'];
		}

	}




	function login()

	{
		$this->meta['description'] = "Imprenta Mar del Plata. Impresiones laser color A3 y A4 de tesis y libros de la Facultad de Medicina, Kinesiología, Fonoaudiología, Enfermería y Educación Fisica. Imprimí tu pdf online.";

		$this->data['meta'] = $this->meta;

		$this->data['seccion'] = "ingresar";

		$this->template->title('Iniciar sesión | PDF Color');

		$this->template->build('sitio/login', $this->data);

	}

	function login_proc()

	{

		$this->load->model('admin/clientes_model','modelo',TRUE);

		$results = $this->modelo->login();



		if ($results['num_rows'] > 0)

		{

			$this->session->set_userdata("user_data", $results['rows']);


			//redirect();
			//redirect('sitio/login', 'refresh');
			$this->template->build('sitio/inicio', $this->data);

		}

		else

		{

			$this->meta['description'] = "Imprenta Mar del Plata. Impresiones laser color A3 y A4 de tesis y libros de la Facultad de Medicina, Kinesiología, Fonoaudiología, Enfermería y Educación Fisica. Imprimí tu pdf online.";

			$this->data['meta'] = $this->meta;

			$this->data['msg'] = "ERROR. Nombre de usuario o contraseña incorrecto.";

			$this->template->title('Iniciar sesión | PDF Color');

			$this->template->build('sitio/home', $this->data);

		}

	}

	function registrarse()

	{
		$this->meta['description'] = "Imprenta Mar del Plata. Impresiones laser color A3 y A4 de tesis y libros de la Facultad de Medicina, Kinesiología, Fonoaudiología, Enfermería y Educación Fisica. Imprimí tu pdf online.";

		$this->data['meta'] = $this->meta;

		$this->data['seccion'] = "registrarse";

		$this->template->title('Registrarse | PDF Color');

		$this->template->build('sitio/registrarse', $this->data);

	}





	function logout()

	{

		//Destruir variable de session de usuario

		$this->session->unset_userdata("user_data");

		//Destruir variable de session del carrito de compras

		$this->session->unset_userdata("cart_products");

		//redireccionar a la pagina en la que estaba actualmente

		redirect($this->input->get('last_url'));

	}



	function nueva_venta()
	{
		$this->load->model('admin/productos_model', 'modelo', TRUE);
		$this->load->model('admin/stockproductos_model', 'stock_model', TRUE);

		$sql['limit'] = NULL;
		$sql['offset'] = NULL;
		$sql['sort_order'] = "asc";
		$sql['sort_by'] = "orden";

		$results = $this->modelo->getAll($sql);
		$productos = $results['rows'];

		$id_sucursal = $this->session->userdata("user_data")[0]->idusuario;

		foreach ($productos as &$producto) {
			$stock_data = $this->stock_model->get_by_producto($producto->id);
			$producto->stock_actual = 0;
			foreach ($stock_data as $stock) {
				if ($stock->idusuario == $id_sucursal) {
					$producto->stock_actual = $stock->stock;
					break;
				}
			}
		}

		$this->data['rows_productos'] = $productos;
		$this->data['num_results'] = $results['num_rows'];

		$this->meta['description'] = "Imprenta Mar del Plata. Impresiones laser color A3 y A4 de tesis y libros de la Facultad de Medicina, Kinesiología, Fonoaudiología, Enfermería y Educación Fisica. Imprimí tu pdf online.";
		$this->meta['keywords'] = "";
		$this->data['meta'] = $this->meta;
		$this->data['seccion'] = "pedido";

		$this->template->title('Nueva venta | PDF Color');
		$this->template->build('sitio/nueva_venta', $this->data);
	}

	function nueva_venta2()
	{

		$this->load->model('admin/productos_model','modelo',TRUE);
		$sql['limit'] = NULL;
		$sql['offset'] = NULL;
		$sql['sort_order'] = "asc";
		$sql['sort_by'] = "orden";

		$results = $this->modelo->getAll($sql);

		$this->data['rows_productos'] = $results['rows'];
		$this->data['num_results'] = $results['num_rows'];


		$this->meta['description'] = "Imprenta Mar del Plata. Impresiones laser color A3 y A4 de tesis y libros de la Facultad de Medicina, Kinesiología, Fonoaudiología, Enfermería y Educación Fisica. Imprimí tu pdf online.";
		$this->meta['keywords'] = "";
		$this->data['meta'] = $this->meta;
		$this->data['seccion'] = "pedido";


		$this->template->title('Nueva venta | PDF Color');
		$this->template->build('sitio/nueva_venta2', $this->data);
	}




	function index()
	{

		$this->meta['description'] = "Imprenta Mar del Plata. Impresiones laser color A3 y A4 de tesis y libros de la Facultad de Medicina, Kinesiología, Fonoaudiología, Enfermería y Educación Fisica. Imprimí tu pdf online. ";
		$this->meta['keywords'] = "";
		$this->data['seccion'] = "home";

		$this->data['meta'] = $this->meta;
		if (isset($this->session->userdata("user_data")[0]->idusuario))
		{
			$this->template->title('PDF Color | Inicio');
			$this->template->build('sitio/inicio', $this->data);
		}
		else
		{
			$this->template->title('PDF Color | Inicio');
			$this->template->build('sitio/login', $this->data);
		}

	}


	function inicio()
	{
		$this->meta['description'] = "Imprenta Mar del Plata. Impresiones laser color A3 y A4 de tesis y libros de la Facultad de Medicina, Kinesiología, Fonoaudiología, Enfermería y Educación Fisica. Imprimí tu pdf online.";
		$this->meta['keywords'] = "";
		$this->data['meta'] = $this->meta;
		$this->data['seccion'] = "contacto";

		$this->template->title('Inicio | PDF Color');
		$this->template->build('sitio/inicio', $this->data);
	}

	function revendedores()
	{
		$this->meta['description'] = "Imprenta Mar del Plata. Impresiones laser color A3 y A4 de tesis y libros de la Facultad de Medicina, Kinesiología, Fonoaudiología, Enfermería y Educación Fisica. Imprimí tu pdf online.";
		$this->meta['keywords'] = "";
		$this->data['meta'] = $this->meta;
		$this->data['seccion'] = "revendedores";

		$this->template->title('Revendedores | PDF Color');
		$this->template->build('sitio/revendedores', $this->data);
	}


	function calculadorprecios()
	{
		$this->meta['description'] = "Imprenta Mar del Plata. Impresiones laser color A3 y A4 de tesis y libros de la Facultad de Medicina, Kinesiología, Fonoaudiología, Enfermería y Educación Fisica. Imprimí tu pdf online.";
		$this->meta['keywords'] = "";
		$this->data['meta'] = $this->meta;
		$this->data['seccion'] = "calculador";

		$this->load->model('admin/precios_model','modelo',TRUE);
		$sql['limit'] = NULL;
		$sql['offset'] = NULL;
		$sql['sort_order'] = "asc";
		$sql['sort_by'] = "orden";

		$results = $this->modelo->getAll($sql);

		$this->data['rows_precios'] = $results['rows'];
		$this->data['num_results'] = $results['num_rows'];

		$this->template->title('Calculador de Precios | PDF Color');
		$this->template->build('sitio/calculadorprecios', $this->data);
	}


	function busqueda($nombre)
	{
		$nombre = str_replace("%20", " ", $nombre);

		$this->data['busqueda'] = $nombre;


		$this->load->model('admin/productos_model','modelo_busqueda',TRUE);
		$sql2['limit'] = NULL;
		$sql2['offset'] = NULL;
		$sql2['activo'] = 1;
		//Where para la búsqueda:
		$sql2['bnombre'] = $nombre;
		$sql2['sort_order'] = "asc";
		$sql2['sort_by'] = "orden";

		$results = $this->modelo_busqueda->getAll($sql2);
		$this->data['rows_productos'] = $results['rows'];
		$this->data['num_results'] = $results['num_rows'];

		$marca = $results['rows'][0]->nombre_marca;
		$categoria = $results['rows'][0]->categoria;


		$this->data['rows'] = $results['rows'];

		foreach($this->data['rows'] as $aux):

			$imagenPrincipal = $this->modelo_busqueda->getImagenPrincipal($aux->id);

			$aux->imagen = (isset($imagenPrincipal[0]->nombre))?$imagenPrincipal[0]->nombre:'no-thumbail.png';

		endforeach;

		$this->load->model('sitio/imagenes_model','modelo_imagenes',TRUE);
		$sql1['limit'] = NULL;
		$sql1['offset'] = NULL;
		$sql1['sort_order'] = "asc";
		$sql1['sort_by'] = "orden";

		$results = $this->modelo_imagenes->getAll($sql1);
		$this->data['row_imagenes'] = $results['rows'];
		$this->data['num_results'] = $results['num_rows'];

		$this->load->model('admin/marcas_model','modelo_marcas',TRUE);
		$sql3['limit'] = NULL;
		$sql3['offset'] = NULL;
		$sql3['sort_order'] = "asc";
		$sql3['sort_by'] = "orden";

		$results = $this->modelo_marcas->getAll($sql3);
		$this->data['rows_marcas'] = $results['rows'];
		$this->data['num_results'] = $results['num_rows'];

		$this->data['rows'] = $results['rows'];


		$this->meta['description'] = "Imprenta Mar del Plata. Impresiones laser color A3 y A4 de tesis y libros de la Facultad de Medicina, Kinesiología, Fonoaudiología, Enfermería y Educación Fisica. Imprimí tu pdf online.";
		$this->meta['keywords'] = "";
		$this->data['meta'] = $this->meta;
		$this->data['seccion'] = "productos";


		$this->template->title('Busqueda | PDF Color');
		$this->template->build('sitio/busqueda', $this->data);
	}

	function blog()
	{
		$this->load->model('admin/blog_model','modelo',TRUE);
		$sql['limit'] = NULL;
		$sql['limit'] = NULL;
		$sql['offset'] = NULL;
		$sql['sort_order'] = "asc";
		$sql['sort_by'] = "orden";

		$results = $this->modelo->getAll($sql);
		$this->data['row_blog'] = $results['rows'];
		$this->data['num_results'] = $results['num_rows'];

		$this->meta['description'] = "Imprenta Mar del Plata. Impresiones laser color A3 y A4 de tesis y libros de la Facultad de Medicina, Kinesiología, Fonoaudiología, Enfermería y Educación Fisica. Imprimí tu pdf online.";
		$this->data['meta'] = $this->meta;
		$this->data['seccion'] = "novedades";


		$this->template->title('Novedades | PDF Color');
		$this->template->build('sitio/blog', $this->data);
	}


	function vista_blog($nombre)
	{

		$this->load->model('admin/blog_model','modelo_blog',TRUE);
		$sql2['limit'] = NULL;
		$sql2['limit'] = NULL;
		$sql2['offset'] = NULL;
		$sql2['sort_order'] = "asc";
		$sql2['sort_by'] = "orden";

		$resultados = $this->modelo_blog->getAll($sql2);
		$this->data['rows_blogs'] = $resultados['rows'];
		$this->data['nums_results'] = $resultados['num_rows'];


		$this->load->model('admin/blog_model','modelo',TRUE);
		$sql['limit'] = NULL;
		$sql['slug'] = $nombre;

		$results = $this->modelo->getAll($sql);
		$this->data['row_blog'] = $results['rows'];
		$this->data['num_results'] = $results['num_rows'];
		$this->data['seccion'] = "novedades";

		$titulo_seo = $results['rows'][0]->titulo_seo;
		$descripcion_seo = $results['rows'][0]->descripcion_seo;

		$this->meta['description'] = $descripcion_seo;

		$this->data['meta'] = $this->meta;


		$this->template->title( $titulo_seo.' | PDF Color');
		$this->template->build('sitio/vista_blog', $this->data);
	}



	function comprar_fuera_mdp()
	{

		$this->load->model('admin/provincia_model','modelo',TRUE);
		$sql['limit'] = NULL;
		$sql['offset'] = NULL;
		$sql['sort_order'] = "asc";
		$sql['sort_by'] = "orden";

		$results = $this->modelo->getAll($sql);

		$this->data['rows_provincias'] = $results['rows'];
		$this->data['num_results'] = $results['num_rows'];

		$this->load->model('admin/precios_model','modelo_precios',TRUE);
		$sql2['limit'] = NULL;
		$sql2['offset'] = NULL;
		$sql2['sort_order'] = "asc";
		$sql2['sort_by'] = "orden";

		$resultas = $this->modelo_precios->getAll($sql2);

		$this->data['rows_precios'] = $resultas['rows'];
		$this->data['num_results'] = $resultas['num_rows'];


		$this->meta['description'] = "Imprenta Mar del Plata. Impresiones laser color A3 y A4 de tesis y libros de la Facultad de Medicina, Kinesiología, Fonoaudiología, Enfermería y Educación Fisica. Imprimí tu pdf online.";
		$this->meta['keywords'] = "";
		$this->data['meta'] = $this->meta;
		$this->data['seccion'] = "comprarfuera";


		$this->template->title('Comprar fuera de MDP | PDF Color');
		$this->template->build('sitio/comprar_fuera_mdp', $this->data);
	}

	function comprar_dentro_mdp()
	{

		$this->load->model('admin/provincia_model','modelo',TRUE);
		$sql['limit'] = NULL;
		$sql['offset'] = NULL;
		$sql['sort_order'] = "asc";
		$sql['sort_by'] = "orden";

		$results = $this->modelo->getAll($sql);

		$this->data['rows_provincias'] = $results['rows'];
		$this->data['num_results'] = $results['num_rows'];

		$this->load->model('admin/precios_model','modelo_precios',TRUE);
		$sql2['limit'] = NULL;
		$sql2['offset'] = NULL;
		$sql2['sort_order'] = "asc";
		$sql2['sort_by'] = "orden";

		$resultas = $this->modelo_precios->getAll($sql2);

		$this->data['rows_precios'] = $resultas['rows'];
		$this->data['num_results'] = $resultas['num_rows'];


		$this->meta['description'] = "Imprenta Mar del Plata. Impresiones laser color A3 y A4 de tesis y libros de la Facultad de Medicina, Kinesiología, Fonoaudiología, Enfermería y Educación Fisica. Imprimí tu pdf online.";
		$this->meta['keywords'] = "";
		$this->data['meta'] = $this->meta;
		$this->data['seccion'] = "comprardentro";


		$this->template->title('Comprar dentro de MDP | PDF Color');
		$this->template->build('sitio/comprar_dentro_mdp', $this->data);
	}

	function mayorista()
	{

		$this->load->model('admin/precios_model','modelo_precios',TRUE);
		$sql22['limit'] = NULL;
		$sql22['limit'] = NULL;
		$sql22['offset'] = NULL;
		$sql22['sort_order'] = "asc";
		$sql22['sort_by'] = "id";

		$resultadoss = $this->modelo_precios->getAll($sql22);
		$this->data['rows_precios'] = $resultadoss['rows'];
		$this->data['nums_results'] = $resultadoss['num_rows'];

		$this->load->model('admin/libro_model','modelo_libro',TRUE);
		$sql2['limit'] = NULL;
		$sql2['limit'] = NULL;
		$sql2['offset'] = NULL;
		$sql2['sort_order'] = "asc";
		$sql2['sort_by'] = "nombre";

		$resultados = $this->modelo_libro->getAll($sql2);
		$this->data['rows_libros'] = $resultados['rows'];
		$this->data['nums_results'] = $resultados['num_rows'];

		$this->meta['description'] = "Imprenta Mar del Plata. Impresiones laser color A3 y A4 de tesis y libros de la Facultad de Medicina, Kinesiología, Fonoaudiología, Enfermería y Educación Fisica. Imprimí tu pdf online.";
		$this->data['meta'] = $this->meta;
		$this->data['seccion'] = "mayorista";


		$this->template->title('Mayorista | PDF Color');
		$this->template->build('sitio/mayorista', $this->data);
	}


	function login_mayorista()

	{
		$this->meta['description'] = "Imprenta Mar del Plata. Impresiones laser color A3 y A4 de tesis y libros de la Facultad de Medicina, Kinesiología, Fonoaudiología, Enfermería y Educación Fisica. Imprimí tu pdf online.";

		$this->data['meta'] = $this->meta;

		$this->data['seccion'] = "ingresar";

		$this->template->title('Iniciar sesión | PDF Color');

		$this->template->build('sitio/login_mayorista', $this->data);

	}

	function login_proc_mayorista()

	{

		$this->load->model('admin/mayorista_model','modelo_mayorista',TRUE);

		$results = $this->modelo_mayorista->login();



		if ($results['num_rows'] > 0)

		{

			$this->session->set_userdata("mayorista", $results['rows']);


			//redirect();

			redirect('/mayorista', 'refresh');

		}

		else

		{

			$this->meta['description'] = "Imprenta Mar del Plata. Impresiones laser color A3 y A4 de tesis y libros de la Facultad de Medicina, Kinesiología, Fonoaudiología, Enfermería y Educación Fisica. Imprimí tu pdf online.";

			$this->data['meta'] = $this->meta;

			$this->data['msg'] = "ERROR. Nombre de usuario o contraseña incorrecto.";

			$this->template->title('Iniciar sesión | PDF Color');

			$this->template->build('sitio/login_mayorista', $this->data);

		}

	}

	function logout_mayorista()

	{

		//Destruir variable de session de usuario

		$this->session->unset_userdata("mayorista");

		//Destruir variable de session del carrito de compras

		$this->session->unset_userdata("cart_products");

		//redireccionar a la pagina en la que estaba actualmente

		redirect($this->input->get('last_url'));

	}


	function getData()
	{
		//Carro
		if (isset($this->session->userdata("user_data")[0]->idusuario))
		{
			$this->load->model('admin/shopping_model','modelo_carro',TRUE);

			$this->modelo_carro->add_item(session_id(), $_GET['idArt'], $_GET['quantity'], $this->session->userdata("user_data")[0]->idusuario);


			$sql['limit'] = NULL;
			$sql['offset'] = NULL;
			/*$sql['where'] = "0";*/
			$sql['sort_order'] = "asc";
			$sql['sort_by'] = "productos.nombre";
			$sql['cod_clie'] = $this->session->userdata("user_data")[0]->idusuario;
			$results = $this->modelo_carro->getAll($sql);
			//$this->data['rows_carro'] = $results['rows'];
			echo json_encode($results['rows']);
		}
	}

	function quitarData()
	{
		//Carro
		if (isset($this->session->userdata("user_data")[0]->idusuario))
		{
			$this->load->model('admin/shopping_model','modelo_carro',TRUE);

			//error_log("--QUITAR--".$_GET['idArt']);
			if ($_GET['delAll'] == 1)
				$this->modelo_carro->clear_cart($this->session->userdata("user_data")[0]->idusuario);
			else
				$this->modelo_carro->delete_item($this->session->userdata("user_data")[0]->idusuario, $_GET['idArt']);


			$sql['limit'] = NULL;
			$sql['offset'] = NULL;
			/*$sql['where'] = "0";*/
			$sql['sort_order'] = "asc";
			$sql['sort_by'] = "productos.nombre";
			$sql['cod_clie'] = $this->session->userdata("user_data")[0]->idusuario;
			$results = $this->modelo_carro->getAll($sql);
			//$this->data['rows_carro'] = $results['rows'];
			echo json_encode($results['rows']);
		}
	}


	function modificarData()
{


    if (isset($this->session->userdata("user_data")[0]->idusuario))
    {
        $this->load->model('admin/shopping_model','modelo_carro',TRUE);
        // El carrito se guarda con el idusuario (igual que getData y quitarData).
        $cliente = (int)$this->session->userdata("user_data")[0]->idusuario;
        $idArt = (int)$this->input->get('idArt');
        $cantidad = (int)$this->input->get('quantity');

        // Actualizar la cantidad (0 o menos quita el producto)
        if ($cantidad > 0)
            $this->modelo_carro->modify_quantity($cliente, $idArt, $cantidad);
        else
            $this->modelo_carro->delete_item($cliente, $idArt);

        // Traer el carrito actualizado con los campos necesarios
        $rows_carro = $this->modelo_carro->get_cart_detailed($cliente);

        echo json_encode($rows_carro);
    }
}

	function enviar_pedido()
	{

		$this->load->model('admin/shopping_model','modelo_carro',TRUE);

		$cliente = $this->session->userdata("user_data")[0]->idusuario;

		$modo_de_pago = $this->input->get('pago');

		$this->modelo_carro->send($cliente,$modo_de_pago);

		$this->modelo_carro->clear_cart($cliente);

		$this->data['rows_carro'] = array();

		$this->template->build('sitio/enviar_pedido', $this->data);
	}

	function set_pago()
	{
		$pago = $this->input->post('pago');

		$this->session->set_userdata('pago', $pago);

		echo json_encode(['ok' => true]);
	}


	// ===================== MODIFICAR VENTA =====================

	// Medios de pago válidos y su columna en cajas_diarias
	private $columnas_medio_pago = array(
		'Efectivo'                  => 'total_efectivo',
		'Tarjeta de crédito/débito' => 'total_tarjeta',
		'Medio electrónico'         => 'total_medio_electronico',
		'Transferencia/QR'          => 'total_transferencia',
	);

	// Devuelve la venta si la sucursal logueada puede modificarla,
	// o un texto con el motivo si no puede.
	private function _venta_modificable($idpedido)
	{
		if (!isset($this->session->userdata("user_data")[0]->idusuario))
			return 'Tenés que iniciar sesión.';

		$sucursal = (int)$this->session->userdata("user_data")[0]->idusuario;

		$pedido = $this->db->where('idpedido', (int)$idpedido)->where('cod_clie', $sucursal)->get('pedidos')->row();
		if (!$pedido)
			return 'La venta no existe.';

		if ($pedido->fecha != date('Y-m-d'))
			return 'Solo se pueden modificar ventas de hoy.';

		$usuario = $this->db->select('caja_cerrada')->where('idusuario', $sucursal)->get('usuarios')->row();
		if ($usuario && $usuario->caja_cerrada == 1)
			return 'La caja de hoy ya está cerrada.';

		return $pedido;
	}

	function modificar_venta($idpedido = 0)
	{
		if (!isset($this->session->userdata("user_data")[0]->idusuario))
			redirect('login');

		$pedido = $this->_venta_modificable($idpedido);
		if (!is_object($pedido))
			redirect('pedido_detalles/'.(int)$idpedido);

		$sucursal = (int)$this->session->userdata("user_data")[0]->idusuario;

		// Productos con el stock de la sucursal (una sola consulta)
		$this->data['rows_productos'] = $this->db->query(
			"SELECT p.id, p.codigo, p.nombre, p.precio_final,
			        COALESCE((SELECT s.stock FROM stock_sucursal s WHERE s.producto_id = p.id AND s.idusuario = ? ORDER BY s.id DESC LIMIT 1), 0) AS stock_actual
			 FROM productos p ORDER BY p.orden ASC, p.nombre ASC",
			array($sucursal)
		)->result();

		$this->data['pedido'] = $pedido;
		$this->data['items'] = $this->db->select('pedidosdetalles.id_articu, pedidosdetalles.cantidad, productos.nombre, productos.codigo, productos.precio_final')
			->from('pedidosdetalles')
			->join('productos', 'productos.id = pedidosdetalles.id_articu', 'left')
			->where('pedidosdetalles.idpedido', (int)$pedido->idpedido)
			->order_by('pedidosdetalles.iddetalle', 'asc')
			->get()->result();
		$this->data['medios_pago'] = array_keys($this->columnas_medio_pago);

		$this->data['meta'] = array('description' => '', 'keywords' => '');
		$this->data['seccion'] = "pedidos";
		$this->template->title('Modificar venta | PDF Color');
		$this->template->build('sitio/modificar_venta', $this->data);
	}

	// Recibe por POST: idpedido, modo_de_pago y items (JSON: [{id, cantidad}, ...])
	function guardar_venta()
	{
		header('Content-Type: application/json; charset=utf-8');

		$idpedido = (int)$this->input->post('idpedido');
		$pedido = $this->_venta_modificable($idpedido);
		if (!is_object($pedido))
		{
			echo json_encode(array('ok' => false, 'error' => $pedido));
			return;
		}

		$sucursal = (int)$this->session->userdata("user_data")[0]->idusuario;

		// ---- Validar todo antes de escribir ----
		$modo_nuevo = (string)$this->input->post('modo_de_pago');
		if (!isset($this->columnas_medio_pago[$modo_nuevo]))
		{
			echo json_encode(array('ok' => false, 'error' => 'Medio de pago inválido.'));
			return;
		}

		$items_recibidos = json_decode((string)$this->input->post('items'), true);
		if (!is_array($items_recibidos))
		{
			echo json_encode(array('ok' => false, 'error' => 'Datos de productos inválidos.'));
			return;
		}

		$cantidades_nuevas = array();
		foreach ($items_recibidos as $item)
		{
			$id = isset($item['id']) ? (int)$item['id'] : 0;
			$cantidad = isset($item['cantidad']) ? (int)$item['cantidad'] : 0;
			if ($id <= 0 || $cantidad <= 0) continue;
			$cantidades_nuevas[$id] = (isset($cantidades_nuevas[$id]) ? $cantidades_nuevas[$id] : 0) + $cantidad;
		}
		if (count($cantidades_nuevas) == 0)
		{
			echo json_encode(array('ok' => false, 'error' => 'La venta tiene que tener al menos un producto.'));
			return;
		}

		$precios = array();
		$filas = $this->db->select('id, precio_final')->where_in('id', array_keys($cantidades_nuevas))->get('productos')->result();
		foreach ($filas as $fila) $precios[(int)$fila->id] = (float)$fila->precio_final;
		if (count($precios) != count($cantidades_nuevas))
		{
			echo json_encode(array('ok' => false, 'error' => 'Algún producto ya no existe.'));
			return;
		}

		$total_nuevo = 0;
		foreach ($cantidades_nuevas as $id => $cantidad) $total_nuevo += $cantidad * $precios[$id];
		$total_nuevo = round($total_nuevo); // pedidos.total no guarda decimales

		$cantidades_anteriores = array();
		$detalle_anterior = $this->db->select('id_articu, cantidad')->where('idpedido', $idpedido)->get('pedidosdetalles')->result();
		foreach ($detalle_anterior as $fila)
		{
			$id = (int)$fila->id_articu;
			$cantidades_anteriores[$id] = (isset($cantidades_anteriores[$id]) ? $cantidades_anteriores[$id] : 0) + (int)$fila->cantidad;
		}

		$total_anterior = (float)$pedido->total;
		$modo_anterior = $pedido->modo_de_pago;

		// ---- Guardar ----
		$this->db->trans_start();

		// 1) Venta y su detalle
		$this->db->where('idpedido', $idpedido)->update('pedidos', array('total' => $total_nuevo, 'modo_de_pago' => $modo_nuevo));
		$this->db->where('idpedido', $idpedido)->delete('pedidosdetalles');
		foreach ($cantidades_nuevas as $id => $cantidad)
		{
			$this->db->insert('pedidosdetalles', array('idpedido' => $idpedido, 'id_articu' => $id, 'cantidad' => $cantidad));
		}

		// 2) Stock: se devuelve lo que se quitó y se descuenta lo que se agregó
		$this->load->model('admin/stockproductos_model', 'modelo_stock', TRUE);
		$productos_afectados = array_unique(array_merge(array_keys($cantidades_anteriores), array_keys($cantidades_nuevas)));
		foreach ($productos_afectados as $id)
		{
			$antes = isset($cantidades_anteriores[$id]) ? $cantidades_anteriores[$id] : 0;
			$ahora = isset($cantidades_nuevas[$id]) ? $cantidades_nuevas[$id] : 0;
			$diferencia = $ahora - $antes;
			if ($diferencia == 0) continue;

			$fila_stock = $this->db->select('stock')->where('producto_id', $id)->where('idusuario', $sucursal)->get('stock_sucursal')->row();
			$stock_actual = $fila_stock ? (int)$fila_stock->stock : 0;
			$this->modelo_stock->save_or_update($id, $sucursal, max(0, $stock_actual - $diferencia));
		}

		// 3) Caja del día: se resta lo anterior y se suma lo nuevo en cada medio de pago
		$sets = array('total = total - '.(float)$total_anterior.' + '.(float)$total_nuevo);
		$ajustes = array();
		if (isset($this->columnas_medio_pago[$modo_anterior]))
			$ajustes[$this->columnas_medio_pago[$modo_anterior]] = -$total_anterior;
		$columna_nueva = $this->columnas_medio_pago[$modo_nuevo];
		$ajustes[$columna_nueva] = (isset($ajustes[$columna_nueva]) ? $ajustes[$columna_nueva] : 0) + $total_nuevo;
		foreach ($ajustes as $columna => $monto)
			$sets[] = $columna.' = '.$columna.' + '.(float)$monto;
		$this->db->query('UPDATE cajas_diarias SET '.implode(', ', $sets).' WHERE id_usuario = ? AND fecha = ?', array($sucursal, $pedido->fecha));

		// 4) Registro del cambio (si la tabla existe)
		if ($this->db->table_exists('pedidos_modificaciones'))
		{
			$this->db->insert('pedidos_modificaciones', array(
				'idpedido'         => $idpedido,
				'id_usuario'       => $sucursal,
				'fecha_hora'       => date('Y-m-d H:i:s'),
				'total_anterior'   => $total_anterior,
				'total_nuevo'      => $total_nuevo,
				'pago_anterior'    => $modo_anterior,
				'pago_nuevo'       => $modo_nuevo,
				'detalle_anterior' => json_encode($cantidades_anteriores),
				'detalle_nuevo'    => json_encode($cantidades_nuevas),
				'ip'               => $this->input->ip_address(),
			));
		}

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE)
		{
			echo json_encode(array('ok' => false, 'error' => 'No se pudo guardar la venta.'));
			return;
		}

		echo json_encode(array('ok' => true, 'total' => $total_nuevo));
	}


	function pedidos()
	{

		$this->load->model('admin/pedidos_model','modelo_pedidos',TRUE);


		$sql['limit'] = NULL;
		$sql['offset'] = NULL;
		/*$sql['where'] = "0";*/
		$sql['sort_order'] = "asc";

		//$sql['sort_by'] = "productos.nombre";
		$sql['cod_clie'] = $this->session->userdata("user_data")[0]->idusuario;

		$sql['fecha'] = date('Y-m-d'); //Traigo los del dia actual.

		$resultado = $this->modelo_pedidos->getAll($sql);

		$this->data['rows_pedidos'] = $resultado['rows'];
		$this->data['num_results'] = $resultado['num_rows'];

		$this->meta['description'] = "Imprenta Mar del Plata. Impresiones laser color A3 y A4 de tesis y libros de la Facultad de Medicina, Kinesiología, Fonoaudiología, Enfermería y Educación Fisica. Imprimí tu pdf online.";

		$this->meta['keywords'] = "";
		$this->data['meta'] = $this->meta;
		$this->data['seccion'] = "pedidos";

		$this->template->title('Ventas | PDF Color');

		$this->template->build('sitio/pedidos', $this->data);
	}

	function pedido_detalles($idpedido)
	{

		$this->load->model('admin/pedidosdetalles_model','modelo_detalles',TRUE);


		$sql['limit'] = NULL;
		$sql['offset'] = NULL;
		/*$sql['where'] = "0";*/
		$sql['sort_order'] = "asc";
		//$sql['sort_by'] = "productos.nombre";
		$sql['id'] = $idpedido;

		$resultado = $this->modelo_detalles->getAll($sql);

		$this->data['rows_detalle'] = $resultado['rows'];
		$this->data['num_results'] = $resultado['num_rows'];

		$this->meta['description'] = "Imprenta Mar del Plata. Impresiones laser color A3 y A4 de tesis y libros de la Facultad de Medicina, Kinesiología, Fonoaudiología, Enfermería y Educación Fisica. Imprimí tu pdf online.";

		$this->meta['keywords'] = "";
		$this->data['meta'] = $this->meta;
		$this->data['seccion'] = "pedidos";

		$this->template->title('Detalles de ventas | PDF Color');

		$this->template->build('sitio/pedido_detalles', $this->data);
	}


	function perfil()

	{

		$this->load->model('admin/impresoras_model','modelo_impresoras',TRUE);


		$sql['limit'] = NULL;
		$sql['offset'] = NULL;
		/*$sql['where'] = "0";*/
		$sql['sort_order'] = "asc";
		//$sql['sort_by'] = "productos.nombre";
		$sql['sub_id'] = $this->session->userdata("user_data")[0]->idusuario;
		$resultado = $this->modelo_impresoras->getAll($sql);

		$this->data['rows_impresoras'] = $resultado['rows'];
		$this->data['num_results'] = $resultado['num_rows'];


		$this->meta['description'] = "Imprenta Mar del Plata. Impresiones laser color A3 y A4 de tesis y libros de la Facultad de Medicina, Kinesiología, Fonoaudiología, Enfermería y Educación Fisica. Imprimí tu pdf online.";

		$this->data['meta'] = $this->meta;

		$this->data['seccion'] = "perfil";

		$this->template->title('Perfil | PDF Color');

		$this->template->build('sitio/perfil', $this->data);

	}


	function cerrar_caja()

	{

		//IMPRESORAS:

		$this->load->model('admin/impresoras_model','modelo_impresoras',TRUE);


		$sql['limit'] = NULL;
		$sql['offset'] = NULL;
		/*$sql['where'] = "0";*/
		$sql['sort_order'] = "asc";
		//$sql['sort_by'] = "productos.nombre";
		$sql['sub_id'] = $this->session->userdata("user_data")[0]->idusuario;
		$resultado = $this->modelo_impresoras->getAll($sql);

		$this->data['rows_impresoras'] = $resultado['rows'];
		$this->data['num_results'] = $resultado['num_rows'];

		//PEDIDOS:

		$this->load->model('admin/pedidos_model','modelo_pedidos',TRUE);


		$sql2['limit'] = NULL;
		$sql2['offset'] = NULL;
		/*$sql['where'] = "0";*/
		$sql2['sort_order'] = "asc";

		//$sql['sort_by'] = "productos.nombre";
		$sql2['cod_clie'] = $this->session->userdata("user_data")[0]->idusuario;

		$sql2['fecha'] = date('Y-m-d'); //Traigo los del dia actual.

		$resultados= $this->modelo_pedidos->getAll($sql2);

		$this->data['rows_pedidos'] = $resultados['rows'];
		$this->data['num_results'] = $resultados['num_rows'];


		$this->meta['description'] = "Imprenta Mar del Plata. Impresiones laser color A3 y A4 de tesis y libros de la Facultad de Medicina, Kinesiología, Fonoaudiología, Enfermería y Educación Fisica. Imprimí tu pdf online.";

		$this->data['meta'] = $this->meta;

		$this->data['seccion'] = "perfil";

		$this->template->title('Cerrar Caja | PDF Color');

		$this->template->build('sitio/cerrar_caja', $this->data);

	}


	function caja_cerrada()

	{

		$this->meta['description'] = "Imprenta Mar del Plata. Impresiones laser color A3 y A4 de tesis y libros de la Facultad de Medicina, Kinesiología, Fonoaudiología, Enfermería y Educación Fisica. Imprimí tu pdf online.";

		$this->data['meta'] = $this->meta;

		$this->data['seccion'] = "perfil";

		$this->template->title('Caja Cerrada | PDF Color');

		$this->template->build('sitio/caja_cerrada', $this->data);

	}





	function router()
	{
		$this->load->view('sitio/router');
	}

}