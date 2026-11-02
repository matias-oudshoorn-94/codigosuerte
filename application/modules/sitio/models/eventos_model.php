<?php
class Eventos_model extends CI_Model{
	private $tabla = 'evento';
	private $id = 'id';
	private $busqueda = 'titulo';
	private $order =  array('id', 'titulo');

	function __construct()
	{
		parent::__construct();
	}

	function getAll($objeto = array())
	{
		if(isset($objeto['sort_order']))
			$sort_order = ($objeto['sort_order'] == 'desc') ? 'desc' : 'asc';
		$sort_columns = $this->order;
		if(isset($objeto['sort_by']))
			$sort_by = (in_array($objeto['sort_by'], $sort_columns)) ? $objeto['sort_by'] : $this->id;

		// Qualification
		if(isset($objeto['bnombre'])) $this->db->like('titulo', $objeto['bnombre']);
		if(isset($objeto['id'])) $this->db->where($this->id, $objeto['id']);
		if(isset($objeto['where'])) $this->db->where('borrado', $objeto['where']); //0-1

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