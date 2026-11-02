<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Pedidos AJAX de las pantallas de venta y cierre de caja.
 *
 * Reemplaza a los scripts sueltos include/impresiones_aprox.php,
 * include/declarar_impresora.php, include/cerrar_caja.php y
 * busqueda/buscar_producto_por_codigo.php. A diferencia de ellos,
 * exige una sucursal logueada y toma su id de la sesion.
 */
class Caja extends MX_Controller
{
	/** Articulos que cuentan como impresiones */
	const ARTICULOS_IMPRESION = array(7, 8, 11);

	/** @var int */
	private $id_sucursal;

	public function __construct()
	{
		parent::__construct();

		$user_data = $this->session->userdata('user_data');
		if (empty($user_data[0]->idusuario))
		{
			$this->output->set_status_header(401);
			exit('Sesión vencida. Volvé a iniciar sesión.');
		}
		$this->id_sucursal = (int) $user_data[0]->idusuario;

		if ($this->input->method() !== 'post')
		{
			$this->output->set_status_header(405);
			exit('Método no permitido');
		}
	}

	/**
	 * Busca un producto por codigo exacto (lector de codigo de barras).
	 * Responde JSON {id, nombre, precio_final} o vacio si no existe.
	 */
	public function producto_por_codigo()
	{
		$codigo = trim((string) $this->input->post('codigo'));
		if ($codigo === '')
		{
			return;
		}

		$producto = $this->db->select('id, nombre, precio_final')
			->where('codigo', $codigo)
			->limit(1)
			->get('productos')
			->row_array();

		if ($producto)
		{
			echo json_encode($producto);
		}
	}

	/**
	 * Impresiones vendidas hoy por la sucursal (segun los pedidos).
	 */
	public function impresiones_aprox()
	{
		$fila = $this->db->select_sum('d.cantidad', 'impresiones')
			->from('pedidos p')
			->join('pedidosdetalles d', 'd.idpedido = p.idpedido')
			->where('p.cod_clie', $this->id_sucursal)
			->where('p.fecha', date('Y-m-d'))
			->where_in('d.id_articu', self::ARTICULOS_IMPRESION)
			->get()
			->row();

		echo (int) ($fila ? $fila->impresiones : 0);
	}

	/**
	 * Registra el contador de una impresora de la sucursal.
	 */
	public function declarar_impresora()
	{
		$codigo         = trim((string) $this->input->post('codigo_impresora'));
		$cantidad_total = $this->input->post('cantidad_total');
		$contador_hoy   = $this->input->post('cantidad_impresiones_hoy');

		if ($codigo === '' || ! is_numeric($cantidad_total) || ! is_numeric($contador_hoy))
		{
			echo 'Error: datos incompletos';
			return;
		}

		// La impresora tiene que pertenecer a la sucursal logueada
		$impresora = $this->db->select('id')
			->where('codigo', $codigo)
			->where('sub_id', $this->id_sucursal)
			->get('impresoras')
			->row();
		if ( ! $impresora)
		{
			echo 'Error: la impresora no pertenece a esta sucursal';
			return;
		}

		$calculo = (int) $cantidad_total - (int) $contador_hoy;

		$this->db->trans_start();
		$this->db->insert('detalles_impresoras', array(
			'id_impresora' => $codigo,
			'fecha'        => date('Y-m-d'),
			'impresiones'  => $calculo,
		));
		$this->db->set('copias', 'copias + '.$calculo, FALSE)
			->where('id', $impresora->id)
			->update('impresoras');
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE)
		{
			echo 'Error: no se pudo guardar';
			return;
		}

		echo "<i class='ti-check' style='margin-right:10px'></i>La impresora código: <b>".html_escape($codigo)."</b> se actualizó con <b>".(int) $cantidad_total."</b> impresiones.<br>"
			."<div style='border: 2px solid #5dc55d;padding: 5px;margin-top: 10px;color: #5dc55d;font-size: 15px;text-align: center;'>Se agregaron: ".$calculo." impresiones del día de hoy</div>";
	}

	/**
	 * Guarda el resumen del dia y marca la caja como cerrada.
	 */
	public function cerrar()
	{
		$numero = function ($campo) {
			$valor = $this->input->post($campo);
			return is_numeric($valor) ? $valor : 0;
		};

		$fecha = date('Y-m-d');
		$datos = array(
			'cantidad_ventas'         => $numero('cantidad_ventas'),
			'total_efectivo'          => $numero('efectivo'),
			'total_tarjeta'           => $numero('tarjeta'),
			'total_medio_electronico' => $numero('electronico'),
			'total_transferencia'     => $numero('transferencia'),
			'total'                   => $numero('total'),
			'impresiones_reales'      => $numero('impresiones_reales'),
			'impresiones_aprox'       => $numero('impresiones_aprox'),
			'desperdicio'             => $numero('desperdicio'),
		);

		$this->db->trans_start();

		$existente = $this->db->select('id')
			->where('id_usuario', $this->id_sucursal)
			->where('fecha', $fecha)
			->order_by('id', 'ASC')
			->limit(1)
			->get('cajas_diarias')
			->row();

		if ($existente)
		{
			$this->db->where('id', $existente->id)->update('cajas_diarias', $datos);
		}
		else
		{
			$this->db->insert('cajas_diarias', $datos + array(
				'id_usuario' => $this->id_sucursal,
				'fecha'      => $fecha,
			));
		}

		$this->db->set('caja_cerrada', 1)
			->where('idusuario', $this->id_sucursal)
			->update('usuarios');

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE)
		{
			echo 'Hubo un error al cerrar la caja, por favor comunicarse con el administrador. Muchas gracias';
			return;
		}

		// La pantalla busca "caja_cerrada" en la respuesta para redirigir
		echo 'caja_cerrada';
	}
}
