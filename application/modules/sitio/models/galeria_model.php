<?php
class Galeria_model extends CI_Model{
	private $tabla = 'ambientacion';
	private $id = 'id';
	private $order =  array('id');
	//private $imagen_campo = 'imagen_interior';
	//private $imagen_campo2 = 'colores';
	
	function __construct()
	{ 
		parent::__construct();
	}

	function getAll($objeto = array())
	{
		
		$this->db->select('ambientacion.id,
							ambientacion.nombre,
							ambientacion.orden,
							ambientacion.imagen,
							'); 
		//$this->db->join('producto', 'producto.id = producto_precio.producto_id', 'left');	
		//$this->db->join('producto_precio', 'producto_precio.producto_id = producto.id', 'left');	
		

		//if(isset($objeto['bnombre'])) $this->db->like('articulos.n_articu', $objeto['bnombre']);
		//if(isset($objeto['bnombre'])) $this->db->or_like('marcas.n_marca', $objeto['bnombre']);
		//if(isset($objeto['bnombre'])) $this->db->or_like('lineas.n_linea', $objeto['bnombre']);
		//if(isset($objeto['bnombre'])) $this->db->or_like('rubros.n_rubro', $objeto['bnombre']);		
		//if(isset($objeto['bnombre'])) $this->db->where("(articulos.c_articu LIKE '%".$objeto['bnombre']."%' OR articulos.n_articu LIKE '%".$objeto['bnombre']."%' OR marcas.n_marca LIKE '%".$objeto['bnombre']."%' OR lineas.n_linea LIKE '%".$objeto['bnombre']."%' OR rubros.n_rubro LIKE '%".$objeto['bnombre']."%')");	
		
		if(isset($objeto['id'])) $this->db->where('ambientacion.id', $objeto['id']);


        // limit / offset
        if (isset($objeto['limit']) && isset($objeto['offset']))
            $this->db->limit($objeto['limit'], ($objeto['limit'] * ($objeto['offset'] - 1)));
        else if (isset($objeto['limit']))
            $this->db->limit($objeto['limit']);

		// order
		if(isset($objeto['sort_by']))
			$this->db->order_by($objeto['sort_by'], $objeto['sort_order']);
		else
			$this->db->order_by('ambientacion.id', 'asc');


		$ret['rows'] = $this->db->get($this->tabla)->result();
		//echo $this->db->last_query();
		// count query
		$q = $this->db->select('COUNT(*) as count', FALSE)
			->from($this->tabla);
		//$this->db->join('producto', 'producto.id = producto_precio.producto_id', 'left');
		//$this->db->join('producto_precio', 'producto_precio.producto_id = producto.id', 'left');

		//if(isset($objeto['bnombre'])) $this->db->like('articulos.n_articu', $objeto['bnombre']);
		//if(isset($objeto['bnombre'])) $this->db->or_like('marcas.n_marca', $objeto['bnombre']);
		//if(isset($objeto['bnombre'])) $this->db->or_like('lineas.n_linea', $objeto['bnombre']);
		//if(isset($objeto['bnombre'])) $this->db->or_like('rubros.n_rubro', $objeto['bnombre']);
		//if(isset($objeto['bnombre'])) $this->db->where("(articulos.c_articu LIKE '%".$objeto['bnombre']."%' OR articulos.n_articu LIKE '%".$objeto['bnombre']."%' OR marcas.n_marca LIKE '%".$objeto['bnombre']."%' OR lineas.n_linea LIKE '%".$objeto['bnombre']."%' OR rubros.n_rubro LIKE '%".$objeto['bnombre']."%')");
		
		if(isset($objeto['id'])) $this->db->where('intro_categorias.id', $objeto['id']);
		
		$tmp = $q->get()->result();
		
		$ret['num_rows'] = $tmp[0]->count;
		
		return $ret;
	}
}

