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


 
	 
	 
	 
	 	
	
	 
	 
	 
	 
	 
		




}