<?php
class Shopping_model extends CI_Model{
	private $tabla = 'shopping';
	private $id = 'id';
	private $busqueda = 'id';
	private $order =  array('productos.nombre');

	function __construct()
	{
		parent::__construct();
	}

	function getAll($objeto = array())
	{

		$this->db->select("productos.id,
							productos.nombre,
							productos.codigo,
							productos.precio,
							productos.porcentaje_marcado,
							productos.precio_final,
							productos.fecha_vencimiento,
							shopping.*
							 ", FALSE);

		$this->db->join('productos', 'productos.id = shopping.product', 'left');
		if(isset($objeto['sort_order']))
			$sort_order = ($objeto['sort_order'] == 'desc') ? 'desc' : 'asc';
		$sort_columns = $this->order;
		if(isset($objeto['sort_by']))
			$sort_by = (in_array($objeto['sort_by'], $sort_columns)) ? $objeto['sort_by'] : $this->id;

		// Qualification
		//if(isset($objeto['bnombre'])) $this->db->like('n_rubro', $objeto['bnombre']);
		//if(isset($objeto['id'])) $this->db->where($this->id, $objeto['id']);
		if(isset($objeto['cod_clie'])) $this->db->where('cod_clie', $objeto['cod_clie']);
		//if(isset($objeto['where'])) $this->db->where('rubros.borrado', $objeto['where']);

		// limit / offset
		if(isset($objeto['limit']) && isset($objeto['offset']))
			$this->db->limit($objeto['offset'], $objeto['limit']);
		else if(isset($objeto['limit']))
			$this->db->limit($objeto['limit']);

		// order
		if(isset($sort_by))
			$this->db->order_by($sort_by, $sort_order);

		$ret['rows'] = $this->db->get($this->tabla)->result();
		//echo $this->db->last_query();
		// count query
		$q = $this->db->select('COUNT(*) as count', FALSE)
			->from($this->tabla);

		$tmp = $q->get()->result();

		$ret['num_rows'] = $tmp[0]->count;

		return $ret;
	}

	function get_cart_detailed($cliente)
{
    $this->db->select('shopping.product, shopping.quantity, productos.nombre, productos.precio_final');
    $this->db->from('shopping');
    $this->db->join('productos', 'productos.id = shopping.product', 'left');
    $this->db->where('shopping.cod_clie', $cliente);
    $query = $this->db->get();
    return $query->result();
}

	/**
	 * Cantidad de un producto en el carrito de la sucursal (0 si no esta).
	 */
	function check_item($cliente, $product)
	{
		$row = $this->db->select('quantity')
			->where('cod_clie', $cliente)
			->where('product', $product)
			->get('shopping')
			->row();

		return $row ? $row->quantity : 0;
	}

	/**
	 * Agrega un producto al carrito o suma la cantidad si ya estaba.
	 */
	function add_item($session, $product, $quantity, $cliente)
	{
		$product  = (int) $product;
		$quantity = (int) $quantity;
		if ($product <= 0 || $quantity <= 0)
		{
			return;
		}

		$qty = (int) $this->check_item($cliente, $product);
		if ($qty == 0)
		{
			$this->db->insert('shopping', array(
				'session'  => (string) $session,
				'product'  => $product,
				'quantity' => $quantity,
				'cod_clie' => $cliente,
			));
		}
		else
		{
			$this->modify_quantity($cliente, $product, $qty + $quantity);
		}
	}

	function delete_item($cliente, $product)
	{
		$this->db->where('cod_clie', $cliente)
			->where('product', $product)
			->delete('shopping');
	}

	function modify_quantity($cliente, $product, $quantity)
	{
		$this->db->set('quantity', (int) $quantity)
			->where('cod_clie', $cliente)
			->where('product', $product)
			->update('shopping');
	}

	function clear_cart($cliente)
	{
		$this->db->where('cod_clie', $cliente)->delete('shopping');
	}

	/**
	 * Total del carrito con los precios actuales de los productos.
	 */
	function cart_total($cliente)
	{
		$row = $this->db->select('SUM(productos.precio_final * shopping.quantity) AS total', FALSE)
			->from('shopping')
			->join('productos', 'productos.id = shopping.product')
			->where('shopping.cod_clie', $cliente)
			->get()
			->row();

		return $row && $row->total !== NULL ? $row->total : 0;
	}

	/**
	 * Convierte el carrito en un pedido: guarda pedido y detalle, descuenta
	 * stock de la sucursal y suma la venta a la caja del dia.
	 */
	function send($cliente, $modo_de_pago)
	{
		$this->load->model('admin/stockproductos_model', 'modelo_stock', TRUE);

		$idusuario_sucursal = $this->session->userdata("user_data")[0]->idusuario;
		$modo_de_pago = (string) $modo_de_pago;
		$total = $this->cart_total($cliente);

		$this->db->set('cod_clie', $cliente)
			->set('total', $total)
			->set('fecha', 'NOW()', FALSE)
			->set('modo_de_pago', $modo_de_pago)
			->insert('pedidos');
		$idpedido = $this->db->insert_id();

		// Detalle + stock
		$items = $this->db->select('product, SUM(quantity) AS quantity', FALSE)
			->where('cod_clie', $cliente)
			->group_by('product')
			->get('shopping')
			->result();

		foreach ($items as $row)
		{
			$this->db->insert('pedidosdetalles', array(
				'idpedido'  => $idpedido,
				'id_articu' => $row->product,
				'cantidad'  => $row->quantity,
			));

			$stock = $this->db->select('stock')
				->where('producto_id', $row->product)
				->where('idusuario', $idusuario_sucursal)
				->get('stock_sucursal')
				->row();

			$nuevo_stock = $stock ? max(0, $stock->stock - $row->quantity) : 0;
			$this->modelo_stock->save_or_update($row->product, $idusuario_sucursal, $nuevo_stock);
		}

		// Sumar la venta a la caja del dia segun el medio de pago
		$fecha_hoy = date('Y-m-d');
		$efectivo = $tarjeta = $medio = $transferencia = 0;
		if ($modo_de_pago == 'Efectivo') {
			$efectivo = $total;
		} elseif ($modo_de_pago == 'Tarjeta de crédito/débito') {
			$tarjeta = $total;
		} elseif ($modo_de_pago == 'Medio electrónico') {
			$medio = $total;
		} elseif ($modo_de_pago == 'Transferencia/QR') {
			$transferencia = $total;
		}

		// Con el indice unico (id_usuario, fecha), si la caja del dia ya existe
		// se suma sobre ella en lugar de duplicarla.
		$this->db->query(
			"INSERT INTO cajas_diarias (id_usuario, fecha, cantidad_ventas, total, total_efectivo, total_tarjeta, total_medio_electronico, total_transferencia)
			 VALUES (?, ?, 1, ?, ?, ?, ?, ?)
			 ON DUPLICATE KEY UPDATE
				cantidad_ventas = cantidad_ventas + 1,
				total = total + VALUES(total),
				total_efectivo = total_efectivo + VALUES(total_efectivo),
				total_tarjeta = total_tarjeta + VALUES(total_tarjeta),
				total_medio_electronico = total_medio_electronico + VALUES(total_medio_electronico),
				total_transferencia = total_transferencia + VALUES(total_transferencia)",
			array($cliente, $fecha_hoy, $total, $efectivo, $tarjeta, $medio, $transferencia)
		);
	}
}
