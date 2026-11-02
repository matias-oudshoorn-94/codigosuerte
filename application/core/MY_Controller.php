<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Base de los controladores del panel de administracion.
 * Exige un usuario logueado que pertenezca al grupo de administradores.
 */
class MY_Controller extends MX_Controller
{
	var $data;

	function __construct()
	{
		parent::__construct();
		$this->data = $this->config->load('sitio', true);
		if ( ! is_array($this->data)) $this->data = array();
		$this->load->helper('panel');

		if ( ! $this->ion_auth->logged_in())
			redirect('admin/login');

		if ( ! $this->ion_auth->is_admin())
		{
			// Usuario sin permisos de administrador: cerrar sesion para no quedar en un bucle
			$this->ion_auth->logout();
			p_flash('Tu usuario no tiene permisos de administrador.', 'error');
			redirect('admin/login');
		}

		$this->template->set_theme('admin');
		$this->template->set_layout('default');
		$this->template->enable_parser(FALSE);

		$this->data['admin_usuario'] = $this->ion_auth->user()->row();
	}

	/**
	 * Muestra una vista dentro del layout del panel.
	 */
	protected function mostrar($vista, $titulo, $seccion)
	{
		$this->data['titulo_pagina'] = $titulo;
		$this->data['seccion'] = $seccion;
		$this->template->title($titulo.' | Administración');
		$this->template->build($vista, $this->data);
	}

	/**
	 * Corta el pedido si no es POST (acciones que modifican datos).
	 */
	protected function solo_post()
	{
		if ($this->input->method() !== 'post')
		{
			show_error('Acción no permitida.', 405);
		}
	}

	/**
	 * Datos de paginacion: ['pagina', 'por_pagina', 'offset', 'total', 'paginas'].
	 */
	protected function paginacion($total, $por_pagina = 25)
	{
		$paginas = max(1, (int) ceil($total / $por_pagina));
		$pagina = min($paginas, max(1, (int) $this->input->get('pagina')));
		return array(
			'pagina'     => $pagina,
			'por_pagina' => $por_pagina,
			'offset'     => ($pagina - 1) * $por_pagina,
			'total'      => (int) $total,
			'paginas'    => $paginas,
		);
	}

	/**
	 * Todas las sucursales (activas e inactivas), indexadas por id.
	 */
	protected function lista_sucursales()
	{
		$lista = array();
		$filas = $this->db->select('idusuario, usuario, nombre, apellido, localidad, domicilio, activo, caja_cerrada')
			->order_by('nombre', 'ASC')->get('usuarios')->result();
		foreach ($filas as $fila)
		{
			$fila->etiqueta = p_nombre_sucursal($fila);
			$lista[(int) $fila->idusuario] = $fila;
		}
		return $lista;
	}
}
