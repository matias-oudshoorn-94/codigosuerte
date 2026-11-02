<?php
class imagenespropiedades_model extends CI_Model{
	private $tabla = 'imagenes';
	private $id = 'id';
	private $busqueda = 'id';
	private $order =  array('orden');
	private $imagen_campo = 'imagen';
	
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
		if(isset($objeto['bnombre'])) $this->db->like($this->busqueda, $objeto['bnombre']);
		if(isset($objeto['id'])) $this->db->where($this->id, $objeto['id']);
				
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
		//echo $this->db->last_query();
		return $this->db->affected_rows();
	}

	function resetPrincipal($tipo)
	{
		$this->db->select('imagen_id');
		$this->db->where('tipo', $tipo);
		$imgs='';
		foreach ($this->db->get('galerias')->result() as $row) $img[]=$row->imagen_id;    
				
		$objeto = array(
            'principal' => 2
        );
        $this->db->where_in('id', $img);
		$this->db->update($this->tabla, $objeto);
		return $this->db->affected_rows();
	}

	function delete($elementos, $propiedad_id)
	{
		$this->db->where_in($this->id, $elementos);
		$this->db->delete($this->tabla);

		$this->db->where_in('imagen_id', $elementos);
		$this->db->where('propiedad_id', $propiedad_id);
		$this->db->delete('galerias');


	}

	function delete_img($id)
	{
		$this->db->select($this->imagen_campo);
		$query = $this->db->get($this->tabla)->row();
		@unlink('archivos/propiedad/original/'.$query->{$this->imagen_campo});
		@unlink('archivos/propiedad/normal/'.$query->{$this->imagen_campo});
		@unlink('archivos/propiedad/thumbs/'.$query->{$this->imagen_campo});
		@unlink('archivos/propiedad/miniatura/'.$query->{$this->imagen_campo});
		
		$objeto = array(
            $this->imagen_campo => NULL
        );
		$this->db->where($this->id, $id);
		$this->db->update($this->tabla, $objeto);
		return $this->db->affected_rows();
	}				
}