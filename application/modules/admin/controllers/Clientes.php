<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Sucursales (URL historica: /admin/clientes) y sus impresoras.
 */
class Clientes extends MY_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('Sucursales_model', 'sucursales_m');
		$this->load->model('admin/clientes_model');
	}

	function index()
	{
		$this->data['lista'] = $this->sucursales_m->listar();
		$this->data['acciones'] = array(
			array('url' => site_url('admin/clientes/edit/0'), 'texto' => 'Nueva sucursal', 'icono' => 'ti-plus', 'clase' => 'principal'),
		);
		$this->mostrar('admin/clientes/list', 'Sucursales', 'sucursales');
	}

	/** Alta (id 0) o edicion de una sucursal. */
	function edit($id = 0)
	{
		$id = (int) $id;
		$sucursal = $id ? $this->sucursales_m->obtener($id) : NULL;
		if ($id && ! $sucursal)
		{
			p_flash('Esa sucursal no existe.', 'error');
			redirect('admin/clientes');
		}

		$errores = array();
		$valores = $sucursal ? (array) $sucursal : array('activo' => 1, 'caja_cerrada' => 2);

		if ($this->input->method() === 'post')
		{
			$campos = array('nombre', 'apellido', 'usuario', 'telefono', 'email', 'domicilio', 'localidad');
			$datos = array();
			foreach ($campos as $campo) $datos[$campo] = trim((string) $this->input->post($campo));
			$datos['activo'] = $this->input->post('activo') == 1 ? 1 : 2;
			$datos['caja_cerrada'] = $this->input->post('caja_cerrada') == 1 ? 1 : 2;
			$clave = (string) $this->input->post('clave');

			if ($datos['nombre'] === '') $errores['nombre'] = 'Poné un nombre para la sucursal.';
			if ($datos['usuario'] === '') $errores['usuario'] = 'El usuario es obligatorio: es con el que se ingresa.';
			elseif ($this->sucursales_m->usuario_en_uso($datos['usuario'], $id)) $errores['usuario'] = 'Ya hay otra sucursal con ese usuario.';
			if ($datos['email'] !== '' && ! filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) $errores['email'] = 'El email no es válido.';
			if ( ! $id && $clave === '') $errores['clave'] = 'Elegí una contraseña para la sucursal.';
			elseif ($clave !== '' && strlen($clave) < 6) $errores['clave'] = 'La contraseña tiene que tener al menos 6 caracteres.';

			if ( ! $errores)
			{
				if ($clave !== '') $datos['clave'] = Clientes_model::encriptar_clave($clave);
				$this->sucursales_m->guardar($id, $datos);
				p_flash($id ? 'Se guardaron los cambios de la sucursal.' : 'Se creó la sucursal.');
				redirect('admin/clientes');
			}
			$valores = array_merge($valores, $datos);
		}

		$this->data['id'] = $id;
		$this->data['valores'] = $valores;
		$this->data['errores'] = $errores;
		$this->data['acciones'] = array(
			array('url' => site_url('admin/clientes'), 'texto' => 'Sucursales', 'icono' => 'ti-arrow-left'),
		);
		$this->mostrar('admin/clientes/edit', $id ? 'Editar sucursal' : 'Nueva sucursal', 'sucursales');
	}

	/**
	 * Elimina una sucursal solo si no tiene ventas; si tiene, hay que desactivarla
	 * para no perder el historial de los reportes.
	 */
	function eliminar()
	{
		$this->solo_post();
		$id = (int) $this->input->post('id');
		if ($this->sucursales_m->cantidad_ventas($id) > 0)
		{
			p_flash('Esa sucursal tiene ventas registradas: desactivala en lugar de eliminarla, así no se pierden los reportes.', 'error');
		}
		else
		{
			$this->sucursales_m->eliminar($id);
			p_flash('Se eliminó la sucursal.');
		}
		redirect('admin/clientes');
	}

	// ---------- Impresoras ----------

	/** Impresoras de una sucursal (URL historica: sub_index). */
	function sub_index($sucursal = 0)
	{
		$s = $this->sucursales_m->obtener($sucursal);
		if ( ! $s)
		{
			p_flash('Esa sucursal no existe.', 'error');
			redirect('admin/clientes');
		}
		$this->data['sucursal'] = $s;
		$this->data['impresoras'] = $this->sucursales_m->impresoras($s->idusuario);
		$this->data['acciones'] = array(
			array('url' => site_url('admin/clientes'), 'texto' => 'Sucursales', 'icono' => 'ti-arrow-left'),
			array('url' => site_url('admin/clientes/sub_edit/0?sucursal='.(int) $s->idusuario), 'texto' => 'Nueva impresora', 'icono' => 'ti-plus', 'clase' => 'principal'),
		);
		$this->mostrar('admin/clientes/sub_list', 'Impresoras de '.p_nombre_sucursal($s), 'sucursales');
	}

	/** Alta (id 0, con ?sucursal=) o edicion de una impresora, con su historial. */
	function sub_edit($id = 0)
	{
		$id = (int) $id;
		$impresora = $id ? $this->sucursales_m->impresora($id) : NULL;
		$sucursal_id = $impresora ? (int) $impresora->sub_id : (int) $this->input->get_post('sucursal');
		$s = $this->sucursales_m->obtener($sucursal_id);
		if (($id && ! $impresora) || ! $s)
		{
			p_flash('No se encontró la impresora o la sucursal.', 'error');
			redirect('admin/clientes');
		}

		$errores = array();
		$valores = $impresora ? (array) $impresora : array('codigo' => '', 'copias' => 0, 'fecha' => date('Y-m-d'));

		if ($this->input->method() === 'post')
		{
			$datos = array(
				'codigo' => trim((string) $this->input->post('codigo')),
				'copias' => $this->input->post('copias'),
				'fecha'  => p_fecha_valida($this->input->post('fecha'), date('Y-m-d')),
				'sub_id' => (int) $s->idusuario,
			);
			if ($datos['codigo'] === '') $errores['codigo'] = 'El código es obligatorio (es el que se elige al cerrar caja).';
			elseif ($this->sucursales_m->codigo_impresora_en_uso($datos['codigo'], $id)) $errores['codigo'] = 'Ya hay otra impresora con ese código.';
			if ( ! is_numeric($datos['copias']) || $datos['copias'] < 0) $errores['copias'] = 'El contador tiene que ser un número mayor o igual a 0.';

			if ( ! $errores)
			{
				$datos['copias'] = (int) $datos['copias'];
				$this->sucursales_m->guardar_impresora($id, $datos);
				p_flash($id ? 'Se guardó la impresora.' : 'Se agregó la impresora.');
				redirect('admin/clientes/sub_index/'.(int) $s->idusuario);
			}
			$valores = array_merge($valores, $datos);
		}

		$this->data['id'] = $id;
		$this->data['sucursal'] = $s;
		$this->data['valores'] = $valores;
		$this->data['errores'] = $errores;
		$this->data['historial'] = $impresora ? $this->sucursales_m->historial_impresora($impresora->codigo) : array();
		$this->data['graficos'] = ! empty($this->data['historial']);
		$this->data['acciones'] = array(
			array('url' => site_url('admin/clientes/sub_index/'.(int) $s->idusuario), 'texto' => 'Impresoras', 'icono' => 'ti-arrow-left'),
		);
		$this->mostrar('admin/clientes/sub_edit', $id ? 'Impresora '.$valores['codigo'] : 'Nueva impresora', 'sucursales');
	}

	function sub_eliminar()
	{
		$this->solo_post();
		$impresora = $this->sucursales_m->impresora($this->input->post('id'));
		if ($impresora)
		{
			$this->sucursales_m->eliminar_impresora($impresora->id);
			p_flash('Se eliminó la impresora '.$impresora->codigo.'.');
			redirect('admin/clientes/sub_index/'.(int) $impresora->sub_id);
		}
		redirect('admin/clientes');
	}
}
