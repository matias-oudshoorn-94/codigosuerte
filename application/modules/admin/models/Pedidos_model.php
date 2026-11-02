<?php
class Pedidos_model extends CI_Model{
	private $tabla = 'pedidos';
	private $id = 'idpedido';
	private $busqueda = 'idpedido';
	private $order =  array('pedidos.fecha');
		
	function __construct()
	{ 
		parent::__construct();
	}

	function getAll($objeto = array())
	{
		
		$this->db->select("pedidos.idpedido,
							pedidos.cod_clie,
							pedidos.fecha,
							pedidos.fecha_hora,
							pedidos.total, 
							pedidos.modo_de_pago"); 
									
		//$this->db->join('productos', 'productos.id = shopping.product', 'left');	
		if(isset($objeto['sort_order']))
			$sort_order = ($objeto['sort_order'] == 'desc') ? 'desc' : 'asc';
		$sort_columns = $this->order;
		if(isset($objeto['sort_by']))
			$sort_by = (in_array($objeto['sort_by'], $sort_columns)) ? $objeto['sort_by'] : $this->id;
				
		// Qualification
		//if(isset($objeto['bnombre'])) $this->db->like('n_rubro', $objeto['bnombre']);
		//if(isset($objeto['id'])) $this->db->where($this->id, $objeto['id']);
		if(isset($objeto['cod_clie'])) $this->db->where('cod_clie', $objeto['cod_clie']);
		if(isset($objeto['fecha'])) $this->db->where('fecha', $objeto['fecha']);

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