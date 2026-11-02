<?php
class usuarios_model extends CI_Model{ /// ???????????? CLIENTES ???????????????????
	private $tabla = 'users';
	private $id = 'id';
	
	function __construct(){ 
		parent::__construct();
	}
	function getAll($objeto = array())
	{
		if(isset($objeto['sort_order']))
			$sort_order = ($objeto['sort_order'] == 'desc') ? 'desc' : 'asc';
		$sort_columns = array('id', 'username', 'first_name');
		if(isset($objeto['sort_by']))
			$sort_by = (in_array($objeto['sort_by'], $sort_columns)) ? $objeto['sort_by'] : $this->id;
				
		// Qualification
		if(isset($objeto['bnombre'])) $this->db->like('username', $objeto['bnombre']);
		if(isset($objeto['id'])) $this->db->where($this->id, $objeto['id']);
		
		// limit / offset
		if(isset($objeto['offset']) && $objeto['offset'] > 1)
			$this->db->limit($objeto['offset'], $objeto['limit']);
		
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
	
	function save($objeto)
	{
		$this->db->insert($this->tabla, $objeto);
		return $this->db->insert_id();
	}
	function update($id, $objeto)
	{
		$this->db->where($this->id, $id);
		$this->db->update($this->tabla, $objeto);
		return $this->db->affected_rows();
	}
	function delete($elementos)
	{
		$this->db->where_in($this->id, $elementos);
		$this->db->delete($this->tabla);
	}
			
}