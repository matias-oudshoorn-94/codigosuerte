<?php

class ventasweb_model extends CI_Model{

	private $tabla = 'ventasweb';

	private $id = 'id';

	private $busqueda = 'descripcion';

	private $order =  array('descripcion');

		

	function __construct()

	{ 

		parent::__construct();

	}



	function getAll($objeto = array())

	{
		$this->db->select($this->tabla.'.*, rubros.nombre as nombre');

		$this->db->join('rubros', 'rubros.id = ventasweb.idrubro', 'left');	

		if(isset($objeto['sort_order']))

			$sort_order = ($objeto['sort_order'] == 'desc') ? 'desc' : 'asc';

		$sort_columns = $this->order;

		if(isset($objeto['sort_by']))

			$sort_by = (in_array($objeto['sort_by'], $sort_columns)) ? $objeto['sort_by'] : $this->id;

				

		// Qualification


		if(isset($objeto['search'])) $this->db->like($this->tabla.'.'.$this->busqueda, $objeto['search']);

		if(isset($objeto['id'])) $this->db->where($this->tabla.'.'.$this->id, $objeto['id']);      
		

		// limit / offset

		if(isset($objeto['limit']) && isset($objeto['offset']))

			$this->db->limit($objeto['limit'], ($objeto['limit']*($objeto['offset']-1)));

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


		if(isset($objeto['search'])) $this->db->like($this->tabla.'.'.$this->busqueda, $objeto['search']);

		

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