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

	function get_cart($cliente)
{
    $query = $this->db->query("SELECT * FROM shopping WHERE cod_clie = '$cliente'");
    return $query->result();
}



	function check_item($cliente, $product)
	{
		$query = "SELECT * FROM shopping WHERE cod_clie='$cliente' AND product='$product' ";

		$result = $this->db->query($query);

		if($result->num_rows() > 0)
		{
			$row =  $result->row();
			return $row->quantity;
		}
		else
		{
			return 0;
		}

	}

	function add_item($session, $product, $quantity, $cliente)
	{
		$qty = $this->check_item($cliente, $product);
		fputs(fopen("qty.txt","w"),print_r($qty,true));
		if($qty == 0)
		{
			$query = "INSERT INTO shopping (session, product, quantity, cod_clie) VALUES ";
			$query .= "('$session', '$product', '$quantity', '$cliente') ";
			error_log($query);
			//log_message('add_item', $query);
			$this->db->query($query);
		}
		else
		{
			$quantity += $qty;
			$query = "UPDATE shopping SET quantity='$quantity' WHERE cod_clie='$cliente' AND ";
			$query .= "product='$product' ";
			error_log($query);
			//log_message('add_item', $query);
			//mail("marcelodangelo@axislab.com","Query", $query, "From: info@fourwitch.com.ar<info@fourwitch.com.ar>\r\n");
			//fputs(fopen("query.txt","w"),print_r($query,true));
			$this->db->query($query);
		}
	}

	function delete_item($cliente, $product)
	{
		$query = "DELETE FROM shopping WHERE cod_clie='$cliente' AND product='$product' ";

		//error_log($query);
		$this->db->query($query);
	}

	function modify_quantity($cliente, $product, $quantity)
	{
		$query = "UPDATE shopping SET quantity='$quantity' WHERE cod_clie='$cliente'
				  AND product='$product' ";
		$this->db->query($query);
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

	function modify_espec($cliente, $product, $espec)
	{
		$query = "UPDATE shopping SET espec = '$espec' WHERE cod_clie='$cliente'
				  AND product='$product' ";
		$this->db->query($query);
	}

	function cart_total($cliente)
	{
		$query = "SELECT * FROM shopping WHERE cod_clie='$cliente' ";
		$result = $this->db->query($query);
		$total = 0;

		if($result->num_rows() > 0)
		{
			//while($row = mysqli_fetch_object($result))
			foreach ($result->result() as $row)
			{
				$query = "SELECT precio_final FROM productos WHERE id='$row->product' ";
				$invResult = $this->db->query($query);
				//$row_price = mysqli_fetch_object($invResult);
				$row_price =  $invResult->row();
				$total += ($row_price->precio_final * $row->quantity);
			}
		}
		return $total;
	}

	function display_contents($cliente)
	{
		$count = 0;
		$query = "SELECT * FROM shopping WHERE cod_clie='$cliente' ORDER BY id ";
		$result = $this->db->query($query);
		//while($row = mysqli_fetch_object($result))
		foreach ($result->result() as $row)
		{
			$query = "SELECT * FROM productos WHERE id='$row->product' ";
			$result_inv = $this->db->query($query);
			//$row_inventory = mysqli_fetch_object($result_inv);
		 	$row_inventory =  $result_inv->row();
			$contents["product"][$count] = $row_inventory->id_articu;
			$contents["price"][$count] = $row_inventory->precio_final;
			$contents["quantity"][$count] = $row->quantity;
			$contents["total"][$count] = ($row_inventory->precio_final * $row->quantity);
			$contents["description"][$count] = $row_inventory->n_articu;
			$contents["espec"][$count] = $row->espec;
			$count++;
		}
		$total = $this->cart_total($cliente);
		$contents["final"] = $total;
		$contents["cantidad"] = $count;
		return $contents;
	}

	function num_items($cliente)
	{
		$query = "SELECT * FROM shopping WHERE cod_clie='$cliente'";
		$result = $this->db->query($query);
		//$num_rows = mysqli_num_rows($result);
		$numRows = $result->num_rows();
		return $num_rows;
	}

	function quant_items($cliente)
	{
		$quant = 0;
		$query = "SELECT * FROM shopping WHERE cod_clie='$cliente'";
		$result = $this->db->query($query);
		//while($row = mysqli_fetch_object($result))
		foreach ($result->result() as $row)	$quant += $row->quantity;
		return $quant;
	}

	function send($cliente, $modo_de_pago)
	{
		$this->load->model('admin/stockproductos_model', 'modelo_stock', TRUE);

		$idusuario_sucursal = $this->session->userdata("user_data")[0]->idusuario;

		$total = $this->cart_total($cliente);

		// 👉 INSERT PEDIDO
		$this->db->query("INSERT INTO pedidos (cod_clie, total, fecha, modo_de_pago)
						  VALUES ('$cliente', '$total', NOW(), '$modo_de_pago')");

		$idpedido = $this->db->insert_id();

		// 👉 DETALLE + STOCK
		$result = $this->db->query("
			SELECT product, SUM(quantity) AS quantity
			FROM shopping
			WHERE cod_clie='$cliente'
			GROUP BY product
		");

		foreach ($result->result() as $row)
		{
			$this->db->query("INSERT INTO pedidosdetalles (idpedido, id_articu, cantidad)
							  VALUES ('$idpedido', '$row->product', '$row->quantity')");

			$this->db->where('producto_id', $row->product);
			$this->db->where('idusuario', $idusuario_sucursal);
			$stock_result = $this->db->get('stock_sucursal');

			$nuevo_stock = 0;

			if ($stock_result->num_rows() > 0) {
				$stock_actual = $stock_result->row()->stock;
				$nuevo_stock = max(0, $stock_actual - $row->quantity);
			}

			$this->modelo_stock->save_or_update($row->product, $idusuario_sucursal, $nuevo_stock);
		}

		// =====================================================
// ✅ ACTUALIZAR CAJA DIARIA (VERSIÓN SIMPLE Y SEGURA)
// =====================================================

$fecha_hoy = date('Y-m-d');

// Inicializar montos
$efectivo = 0;
$tarjeta = 0;
$medio = 0;
$transferencia = 0;

// Sumar SOLO esta venta según medio de pago
if ($modo_de_pago == 'Efectivo') {
    $efectivo = $total;
} elseif ($modo_de_pago == 'Tarjeta de crédito/débito') {
    $tarjeta = $total;
} elseif ($modo_de_pago == 'Medio electrónico') {
    $medio = $total;
} elseif ($modo_de_pago == 'Transferencia/QR') {
    $transferencia = $total;
}

// Buscar si ya existe caja del día
$caja = $this->db->get_where('cajas_diarias', [
    'fecha' => $fecha_hoy,
    'id_usuario' => $cliente
])->row();

if ($caja) {
    // UPDATE (sumando)
    $this->db->where('id', $caja->id);
    $this->db->update('cajas_diarias', [
        'cantidad_ventas' => $caja->cantidad_ventas + 1,
        'total' => $caja->total + $total,
        'total_efectivo' => $caja->total_efectivo + $efectivo,
        'total_tarjeta' => $caja->total_tarjeta + $tarjeta,
        'total_medio_electronico' => $caja->total_medio_electronico + $medio,
        'total_transferencia' => $caja->total_transferencia + $transferencia
    ]);
} else {
    // INSERT (primera venta del día)
    $this->db->insert('cajas_diarias', [
        'id_usuario' => $cliente,
        'fecha' => $fecha_hoy,
        'cantidad_ventas' => 1,
        'total' => $total,
        'total_efectivo' => $efectivo,
        'total_tarjeta' => $tarjeta,
        'total_medio_electronico' => $medio,
        'total_transferencia' => $transferencia
    ]);
}
	}




	function clear_cart($cliente)
	{
		$query = "DELETE FROM shopping WHERE cod_clie='$cliente' ";
		$this->db->query($query);
	}

	function display_pedidos($cliente)
	{
		$count = 0;
		$query = "SELECT * FROM shopping WHERE cod_clie='$cliente'";
		$result = $this->db->query($query);
		//while($row = mysqli_fetch_object($result))
		foreach ($result->result() as $row)
		{
			$query = "SELECT * FROM productos WHERE id='$row->product' ";
			$result_inv = $this->db->query($query);
			//$row_inventory = mysqli_fetch_object($result_inv);
			$row_inventory =  $result_inv->row();
			$contents["product"][$count] = $row_inventory->id;
			$contents["price"][$count] = $row_inventory->precio_final;
			$contents["quantity"][$count] = $row->quantity;
			$contents["total"][$count] = ($row_inventory->precio_final * $row->quantity);
			$count++;
		}
		$total = $this->cart_total($cliente);
		$contents["final"] = $total;
		$contents["cantidad"] = $count;
		return $contents;
	}




}