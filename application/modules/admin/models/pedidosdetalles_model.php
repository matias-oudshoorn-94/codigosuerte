<?php
class Pedidosdetalles_model extends CI_Model{
	private $tabla = 'pedidosdetalles';
	private $id = 'iddetalle';
	private $busqueda = 'iddetalle';
	private $order =  array('pedidosdetalles.iddetalle');
		
	function __construct()
	{ 
		parent::__construct();
	}

	function getAll($objeto = array())
	{
		
		$this->db->select("pedidosdetalles.iddetalle,
							pedidosdetalles.idpedido as id_pedido,
							pedidosdetalles.id_articu,
							pedidosdetalles.cantidad,
							productos.nombre as nombre,
							productos.codigo as codigo,
							pedidos.total as total,
							pedidos.fecha as fecha,
							pedidos.idpedido as pedido"); 
									
		$this->db->join('productos', 'productos.id = pedidosdetalles.id_articu', 'left');	
		$this->db->join('pedidos', 'pedidos.idpedido = pedidosdetalles.idpedido', 'left');	
		if(isset($objeto['sort_order']))
			$sort_order = ($objeto['sort_order'] == 'desc') ? 'desc' : 'asc';
		$sort_columns = $this->order;
		if(isset($objeto['sort_by']))
			$sort_by = (in_array($objeto['sort_by'], $sort_columns)) ? $objeto['sort_by'] : $this->id;
				
		// Qualification
		//if(isset($objeto['bnombre'])) $this->db->like('n_rubro', $objeto['bnombre']);
		//if(isset($objeto['id'])) $this->db->where($this->id, $objeto['id']);
		if(isset($objeto['id'])) $this->db->where('pedidosdetalles.idpedido', $objeto['id']);		
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
	
	function modify_espec($cliente, $product, $espec) 
	{ 
		$query = "UPDATE shopping SET espec = '$espec' WHERE cod_clie='$cliente' 
				  AND product='$product' ";
		$this->db->query($query); 
	} 	
	
	function clear_cart($cliente) 
	{ 
		$query = "DELETE FROM shopping WHERE cod_clie='$cliente' "; 
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
				$query = "SELECT precio FROM productos WHERE id='$row->product' "; 
				$invResult = $this->db->query($query); 
				//$row_price = mysqli_fetch_object($invResult); 
				$row_price =  $invResult->row(); 
				$total += ($row_price->precio * $row->quantity); 
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
			$contents["price"][$count] = $row_inventory->precio; 
			$contents["quantity"][$count] = $row->quantity; 
			$contents["total"][$count] = ($row_inventory->precio * $row->quantity); 
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
	 
	function send($cliente) 
	{ 
		$total = $this->cart_total($cliente);
		$query = "INSERT INTO pedidos (cod_clie, total) 
				  VALUES ('$cliente', '$total') ";
		$this->db->query($query);
		//$idpedido = mysqli_insert_id();
		$idpedido = $this->db->insert_id();

		
		//$query = "SELECT * FROM shopping WHERE cod_clie='$cliente' ORDER BY id ";
		$query = "SELECT session, product, sum(quantity) as quantity, card, id, cod_clie FROM shopping WHERE cod_clie='$cliente' GROUP by product ORDER BY id ";
		$result = $this->db->query($query);
		//while($row = mysqli_fetch_object($result)) 
		foreach ($result->result() as $row)			
		{ 
			$query = "SELECT * FROM productos WHERE id='$row->product' "; 
			$result_inv = $this->db->query($query); 
			//$row_inventory = mysqli_fetch_object($result_inv); 
			$row_inventory =  $result_inv->row();			
			
			$queryInsert = "INSERT INTO pedidosdetalles (idpedido, id_articu, cantidad) VALUES ('$idpedido', '$row_inventory->id', '$row->quantity')";
			$this->db->query($queryInsert); 
		}
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
			$contents["price"][$count] = $row_inventory->precio; 
			$contents["quantity"][$count] = $row->quantity; 
			$contents["total"][$count] = ($row_inventory->precio * $row->quantity);  
			$count++; 
		} 
		$total = $this->cart_total($cliente); 
		$contents["final"] = $total; 
		$contents["cantidad"] = $count; 
		return $contents; 
	}	




}