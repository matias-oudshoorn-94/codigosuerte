<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Entrada al panel: inicio (resumen del dia), login, logout y "Mi cuenta".
 * Extiende MX_Controller (no MY_Controller) porque el login es publico.
 */
class Admin extends MX_Controller
{
	var $data = array();

	function __construct()
	{
		parent::__construct();
		$this->load->helper('panel');
	}

	/** Exige administrador logueado y prepara el layout (igual que MY_Controller). */
	private function requerir_admin()
	{
		if ( ! $this->ion_auth->logged_in())
			redirect('admin/login');

		if ( ! $this->ion_auth->is_admin())
		{
			$this->ion_auth->logout();
			p_flash('Tu usuario no tiene permisos de administrador.', 'error');
			redirect('admin/login');
		}

		$this->template->set_theme('admin');
		$this->template->set_layout('default');
		$this->template->enable_parser(FALSE);
		$this->data['admin_usuario'] = $this->ion_auth->user()->row();
	}

	private function mostrar($vista, $titulo, $seccion)
	{
		$this->data['titulo_pagina'] = $titulo;
		$this->data['seccion'] = $seccion;
		$this->template->title($titulo.' | Administración');
		$this->template->build($vista, $this->data);
	}

	/**
	 * Inicio: lo que pasa hoy en las sucursales y cómo viene el mes.
	 */
	function index()
	{
		$this->requerir_admin();
		$this->load->model('Reportes_model', 'reportes');

		$hoy = date('Y-m-d');
		$inicio_mes = date('Y-m-01');
		$dia_del_mes = (int) date('j');

		// Mismo tramo del mes anterior (del 1 al mismo dia, o al ultimo dia si el mes es mas corto)
		$mes_anterior = new DateTime($inicio_mes);
		$mes_anterior->modify('-1 month');
		$dias_mes_anterior = (int) $mes_anterior->format('t');
		$desde_anterior = $mes_anterior->format('Y-m-01');
		$hasta_anterior = $mes_anterior->format('Y-m-').str_pad(min($dia_del_mes, $dias_mes_anterior), 2, '0', STR_PAD_LEFT);

		$this->data['hoy']          = $this->reportes->resumen($hoy, $hoy);
		$this->data['ayer']         = $this->reportes->resumen(date('Y-m-d', strtotime('-1 day')), date('Y-m-d', strtotime('-1 day')));
		$this->data['mes']          = $this->reportes->resumen($inicio_mes, $hoy);
		$this->data['mes_anterior'] = $this->reportes->resumen($desde_anterior, $hasta_anterior);

		$desde_30 = date('Y-m-d', strtotime('-29 days'));
		$this->data['ultimos_30'] = $this->reportes->serie($desde_30, $hoy);
		$this->data['medios_mes'] = $this->reportes->por_medio($inicio_mes, $hoy);

		// Estado de cada sucursal activa hoy
		$sucursales = $this->db->select('idusuario, usuario, nombre, apellido, localidad, domicilio, caja_cerrada')
			->where('activo', 1)->order_by('nombre', 'ASC')->get('usuarios')->result();
		$ventas_hoy = array();
		foreach ($this->reportes->hoy_por_sucursal() as $fila) $ventas_hoy[(int) $fila->sucursal] = $fila;
		foreach ($sucursales as $s)
		{
			$s->etiqueta = p_nombre_sucursal($s);
			$s->hoy = isset($ventas_hoy[(int) $s->idusuario]) ? $ventas_hoy[(int) $s->idusuario] : NULL;
		}
		$this->data['sucursales'] = $sucursales;

		$nombres = array();
		foreach ($this->db->select('idusuario, usuario, nombre, apellido')->get('usuarios')->result() as $s) $nombres[(int) $s->idusuario] = p_nombre_sucursal($s);
		$this->data['nombres'] = $nombres;
		$this->data['ultimos_cierres'] = $this->reportes->ultimos_cierres(6);
		$this->data['codigos_repetidos'] = $this->reportes->codigos_repetidos();

		$this->data['graficos'] = TRUE;
		$this->data['acciones'] = array(
			array('url' => site_url('admin/reportes'), 'texto' => 'Ver reportes', 'icono' => 'ti-bar-chart'),
		);
		$this->mostrar('admin/inicio', 'Inicio', 'inicio');
	}

	/**
	 * Login del panel.
	 */
	function login()
	{
		if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin())
			redirect('admin');

		$this->data['error'] = '';
		$this->data['usuario'] = '';
		$aviso = $this->session->flashdata('panel_aviso');
		if (is_array($aviso) && $aviso['tipo'] === 'error') $this->data['error'] = $aviso['texto'];

		if ($this->input->method() === 'post')
		{
			$usuario = trim((string) $this->input->post('identity'));
			$clave = (string) $this->input->post('password');
			$this->data['usuario'] = $usuario;

			if ($usuario === '' || $clave === '')
			{
				$this->data['error'] = 'Completá el usuario y la contraseña.';
			}
			elseif ($this->ion_auth->login($usuario, $clave, (bool) $this->input->post('recordar')))
			{
				if ($this->ion_auth->is_admin())
					redirect('admin');

				$this->ion_auth->logout();
				$this->data['error'] = 'Tu usuario no tiene permisos de administrador.';
			}
			else
			{
				$this->data['error'] = 'Usuario o contraseña incorrectos.';
			}
		}

		$this->load->view('admin/login', $this->data);
	}

	function logout()
	{
		$this->ion_auth->logout();
		redirect('admin/login');
	}

	/**
	 * Mi cuenta: datos personales y cambio de contraseña del administrador logueado.
	 */
	function cuenta()
	{
		$this->requerir_admin();
		$usuario = $this->data['admin_usuario'];
		$this->data['errores'] = array();
		$minimo = (int) $this->config->item('min_password_length', 'ion_auth');

		if ($this->input->method() === 'post')
		{
			$accion = $this->input->post('accion');

			if ($accion === 'datos')
			{
				$email = trim((string) $this->input->post('email'));
				$datos = array(
					'first_name' => trim((string) $this->input->post('first_name')),
					'last_name'  => trim((string) $this->input->post('last_name')),
				);
				if ($email !== '' && ! filter_var($email, FILTER_VALIDATE_EMAIL))
				{
					$this->data['errores'][] = 'El email no es válido.';
				}
				else
				{
					$datos['email'] = $email;
					$this->ion_auth->update($usuario->id, $datos);
					p_flash('Tus datos se guardaron.');
					redirect('admin/admin/cuenta');
				}
			}
			elseif ($accion === 'clave')
			{
				$actual = (string) $this->input->post('actual');
				$nueva = (string) $this->input->post('nueva');
				$repetir = (string) $this->input->post('repetir');

				if (strlen($nueva) < $minimo)
					$this->data['errores'][] = 'La contraseña nueva tiene que tener al menos '.$minimo.' caracteres.';
				elseif ($nueva !== $repetir)
					$this->data['errores'][] = 'Las contraseñas nuevas no coinciden.';
				elseif ( ! $this->ion_auth->change_password($usuario->username, $actual, $nueva))
					$this->data['errores'][] = 'La contraseña actual no es correcta.';
				else
				{
					p_flash('Tu contraseña se cambió.');
					redirect('admin/admin/cuenta');
				}
			}
		}

		$this->data['minimo_clave'] = $minimo;
		$this->mostrar('admin/cuenta', 'Mi cuenta', 'cuenta');
	}
}
