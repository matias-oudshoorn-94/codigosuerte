<?php
class Subcategorias_model extends CI_Model{
	private $tabla = 'subcategoria as sub';
	private $id = 'sub.id';
	private $busqueda = 'sub.nombre';
	private $order =  array('sub.id','sub.nombre');
		
	function __construct()
	{ 
		parent::__construct();
	}

	function getAll($objeto = array())
	{
		$this->db->join('categoria', 'categoria.id = sub.idCategoria', 'left');	
		$this->db->select('sub.nombre AS sub_nombre', FALSE);
		$this->db->select('sub.id AS sub_id', FALSE);
		$this->db->select('sub.idCategoria AS sub_idCategoria', FALSE);
		$this->db->select('categoria.id');
		$this->db->select('categoria.nombre');  
		if(isset($objeto['sort_order']))
			$sort_order = ($objeto['sort_order'] == 'desc') ? 'desc' : 'asc';
		$sort_columns = $this->order;
		if(isset($objeto['sort_by']))
			$sort_by = (in_array($objeto['sort_by'], $sort_columns)) ? $objeto['sort_by'] : $this->id;
				
		// Qualification
		if(isset($objeto['bnombre'])) $this->db->like('sub_nombre', $objeto['bnombre']);
		if(isset($objeto['id'])) $this->db->where($this->id, $objeto['id']);
		if(isset($objeto['id'])) $this->db->where('id', $objeto['id']);		
		//if(isset($objeto['where'])) $this->db->where('rubros.borrado', $objeto['where']);		
				
        // limit / offset
        if (isset($objeto['limit']) && isset($objeto['offset']))
            $this->db->limit($objeto['limit'], ($objeto['limit'] * ($objeto['offset'] - 1)));
        else if (isset($objeto['limit']))
            $this->db->limit($objeto['limit']);
			

		
		// order
		if(isset($sort_by))
			$this->db->order_by($sort_by, $sort_order);
			
		$ret['rows'] = $this->db->get($this->tabla)->result();
		//echo $this->db->last_query(); // Para poder ver la Query
		// count query
		$q = $this->db->select('COUNT(*) as count', FALSE)
			->from($this->tabla);

		$tmp = $q->get()->result();		$ret['num_rows'] = $tmp[0]->count;
		
		return $ret;
	}

}