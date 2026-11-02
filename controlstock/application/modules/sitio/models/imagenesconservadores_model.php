<?php
class imagenesconservadores_model extends CI_Model{
	private $tabla = 'imagenes_conservadores';
	private $id = 'id';
	private $order =  array('id', 'orden');

	function __construct()
	{
		parent::__construct();
	}

	function getAll($objeto = array())
	{

		$this->db->select('imagenes_conservadores.id,
							imagenes_conservadores.nombre,
							imagenes_conservadores.orden,
							imagenes_conservadores.sub_id,
							imagenes_conservadores.principal,
							conservador.id,
							');


		$this->db->join('conservador', 'conservador.id = imagenes_conservadores.sub_id', 'left');

		if(isset($objeto['sub_id'])) $this->db->where('imagenes_conservadores.sub_id', $objeto['sub_id']);
		if(isset($objeto['id'])) $this->db->where('imagenes_conservadores.id', $objeto['id']);
		if(isset($objeto['imagenes'])) $this->db->where('imagenes_conservadores.id', $objeto['imagenes']);	


        // limit / offset
        if (isset($objeto['limit']) && isset($objeto['offset']))
            $this->db->limit($objeto['limit'], ($objeto['limit'] * ($objeto['offset'] - 1)));
        else if (isset($objeto['limit']))
            $this->db->limit($objeto['limit']);

		// order
		if(isset($objeto['sort_by']))
			$this->db->order_by($objeto['sort_by'], $objeto['sort_order']);
		else
			$this->db->order_by('imagenes_conservadores.id', 'asc');


		$ret['rows'] = $this->db->get($this->tabla)->result();
		//echo "<br><br><br><br><br><br><br><br><br><br><br><br>";
		//echo $this->db->last_query();
		// count query
		$q = $this->db->select('COUNT(*) as count', FALSE)
			->from($this->tabla);

		$this->db->join('conservador', 'conservador.id = imagenes_conservadores.sub_id', 'left');
		
		if(isset($objeto['sub_id'])) $this->db->where('imagenes_conservadores.sub_id', $objeto['sub_id']);
		if(isset($objeto['id'])) $this->db->where('imagenes_conservadores.id', $objeto['id']);
		if(isset($objeto['imagenes'])) $this->db->where('imagenes_conservadores.id', $objeto['imagenes']);	
		
		$tmp = $q->get()->result();
		
		$ret['num_rows'] = $tmp[0]->count;
		
		return $ret;
	}


}

