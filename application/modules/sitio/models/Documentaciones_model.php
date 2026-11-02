<?php
class documentaciones_model extends CI_Model{
	private $tabla = 'documentacion';
	private $id = 'id';
	private $order =  array('id', 'orden');

	function __construct()
	{
		parent::__construct();
	}

	function getAll($objeto = array())
	{

		$this->db->select('documentacion.id,
							documentacion.nombre,
							documentacion.descripcion,
							documentacion.idcategoria,
							documentacion.orden,
							documentacion.archivo,
							categorias_documentos.id,
							categorias_documentos.nombre,
							categorias_documentos.slug,
							');

		$this->db->join('categorias_documentos', 'categorias_documentos.id = documentacion.idcategoria', 'left');

		if(isset($objeto['id'])) $this->db->where('documentacion.id', $objeto['id']);
		if(isset($objeto['documentacion'])) $this->db->where('documentacion.id', $objeto['documentacion']);
		if(isset($objeto['categoria'])) $this->db->where("(categorias_documentos.slug = '".$objeto['categoria']."' OR categorias_documentos.slug IN (SELECT m1.slug as slug FROM `categorias_documentos` m1 left outer join categorias_documentos m2 on m1.padre = m2.id WHERE m2.slug = '".$objeto['categoria']."'))");


        // limit / offset
        if (isset($objeto['limit']) && isset($objeto['offset']))
            $this->db->limit($objeto['limit'], ($objeto['limit'] * ($objeto['offset'] - 1)));
        else if (isset($objeto['limit']))
            $this->db->limit($objeto['limit']);

		// order
		if(isset($objeto['sort_by']))
			$this->db->order_by($objeto['sort_by'], $objeto['sort_order']);
		else
			$this->db->order_by('documentacion.id', 'asc');


		$ret['rows'] = $this->db->get($this->tabla)->result();
		//echo $this->db->last_query();
		// count query
		$q = $this->db->select('COUNT(*) as count', FALSE)
			->from($this->tabla);


		if(isset($objeto['id'])) $this->db->where('documentacion.id', $objeto['id']);
		if(isset($objeto['documentacion'])) $this->db->where('documentacion.id', $objeto['documentacion']);
		if(isset($objeto['categoria'])) $this->db->where("(categorias_documentos.slug = '".$objeto['categoria']."' OR categorias_documentos.slug IN (SELECT m1.slug as slug FROM `categorias_documentos` m1 left outer join categorias_documentos m2 on m1.padre = m2.id WHERE m2.slug = '".$objeto['categoria']."'))");


		$tmp = $q->get()->result();

		$ret['num_rows'] = $tmp[0]->count;

		return $ret;
	}


		function getCategoria($categoria){



		$this->db->select('categorias_documentos.nombre as categorias_documentos, categorias_documentos.id, slug ');

		$this->db->join('categorias_documentos', 'categorias_documentos.id = '.$this->tabla.'.id_categoria', 'left');

		$this->db->distinct();

		$this->db->where('idcategoria',$categoria);

		$this->db->order_by("orden", "asc");

		$ret['rows'] = $this->db->get($this->tabla)->result();

		$this->db->last_query();

		return $ret;

	}



}

