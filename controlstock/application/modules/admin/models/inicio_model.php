<?php

class inicio_model extends CI_Model

{

	private $tabla = 'pedidos';

	private $id = 'id';

	private $busqueda = 'id';

	private $order =  array('id');



	function __construct()

	{

		parent::__construct();

	}



	function resetPrincipal($principal,$sub_id)

	{

		$this->db->select('id');

		$this->db->where(array('principal' => $principal, 'sub_id' => $sub_id));

		foreach ($this->db->get('imagenes')->result() as $row);



		$objeto = array(

            'principal' => 2

        );

        $this->db->where_in('sub_id', $sub_id);

		$this->db->update('imagenes', $objeto);

		return $this->db->affected_rows();

	}



	function deleteImg($elementos, $sub_id)

	{

		$this->db->where_in('id', $elementos);

		$this->db->where('sub_id', $sub_id);

		$this->db->delete('imagenes');

	}



	function sub_update($objeto)

	{

		$this->db->where('id', $objeto['id']);

		$this->db->update('imagenes', $objeto);

		return $this->db->affected_rows();

	}



	function saveImg($objeto)

	{

		$this->db->insert('imagenes', $objeto);

		return $this->db->insert_id();

	}



	function save($objeto)

	{

		$this->db->insert($this->tabla, $objeto);

		return $this->db->insert_id();

	}



	function getImg($objeto = array()){

		$this->db->where('id', $objeto['id']);

		$this->db->order_by("orden", "asc");

		$ret['rows'] = $this->db->get('imagenes')->result();

		//echo $this->db->last_query();

		return $ret;



	}



	function getAllList($objeto = array())

	{
		$this->db->where('sub_id', $objeto['sub_id']);

		$this->db->order_by("orden", "asc");

		$ret['rows'] = $this->db->get('imagenes')->result();

		//echo $this->db->last_query();

		return $ret;

	}



	function getCamposTabla(){

		return $this->db->list_fields('fermentador');

	}



	function getCampos(){

		return $this->campos;

	}



	function getAll($objeto = array())

	{

		//--Join--

		
		$this->db->select($this->tabla.'.*, usuarios.nombre as usuario, usuarios.apellido as apellido, usuarios.telefono as telefono, usuarios.email as email');
		$this->db->join('usuarios', 'usuarios.idusuario = '.$this->tabla.'.id_usuario', 'left');

		//$this->db->select($this->tabla.'.*, usuarios.nombre as usuario, usuarios.apellido as apellido, usuarios.telefono as telefono, usuarios.email as email, detallespedidos.iddetalle as detalle, detallespedidos.idpedido as detalle_pedido, detallespedidos.idarchivo as detalle_archivo');
		//$this->db->join('usuarios', 'usuarios.idusuario = '.$this->tabla.'.id_usuario', 'left');
		//$this->db->join('detallespedidos', 'detallespedidos.idpedido = '.$this->tabla.'.id', 'left');



		//--

		if(isset($objeto['sort_order']))

			$sort_order = ($objeto['sort_order'] == 'desc') ? 'desc' : 'asc';

		$sort_columns = $this->order;

		if(isset($objeto['sort_by']))

			$sort_by = (in_array($objeto['sort_by'], $sort_columns)) ? $objeto['sort_by'] : $this->id;



		// Qualification

		//if(isset($objeto['search'])) $this->db->like($this->tabla.'.'.$this->busqueda, $objeto['search']);

		if(isset($objeto['search'])) $this->db->where("(pedidos.id LIKE '%".$objeto['search']."%' OR usuarios.nombre LIKE '%".$objeto['search']."%' OR usuarios.apellido LIKE '%".$objeto['search']."%' OR usuarios.email LIKE '%".$objeto['search']."%' OR pedidos.fecha LIKE '%".$objeto['search']."%')");

		if(isset($objeto['id'])) $this->db->where($this->tabla.'.'.$this->id, $objeto['id']);

		//if(isset($objeto['id_nombre'])) $this->db->where('producto.slug', $objeto['id_nombre']);

		//if(isset($objeto['activo'])) $this->db->where('activo', '1');

		//if(isset($objeto['usuario'])) $this->db->where('usuario', '1');

		if(isset($objeto['usuarios'])) $this->db->where('usuarios.idusuario', $objeto['usuarios']);

		//if(isset($objeto['detallespedidos'])) $this->db->where('detallespedidos.iddetalle', $objeto['detallespedidos']);


		//if(isset($objeto['marca'])) $this->db->where('marcas.slug', $objeto['marca']);

		//if(isset($objeto['bnombre'])) $this->db->where("(archivos.codigo LIKE '%".$objeto['bnombre']."%' OR marcas.nombre LIKE '%".$objeto['bnombre']."%' OR producto.nombre LIKE '%".$objeto['bnombre']."%' OR categorias_productos.nombre LIKE '%".$objeto['bnombre']."%' OR producto.descripcion LIKE '%".$objeto['bnombre']."%')");





		// limit / offset

		if(isset($objeto['limit']) && isset($objeto['offset']))

			$this->db->limit($objeto['limit'], ($objeto['limit']*($objeto['offset']-1)));

		else if(isset($objeto['limit']))

			$this->db->limit($objeto['limit']);



		// order

		if(isset($sort_by))

			$this->db->order_by($sort_by, $sort_order);
			//$this->db->order_by('id', 'desc');



		$ret['rows'] = $this->db->get($this->tabla)->result();

		//echo $this->db->last_query();

		// count query

		$q = $this->db->select('COUNT(*) as count', FALSE)

			->from($this->tabla);

		$this->db->join('usuarios', 'usuarios.idusuario = '.$this->tabla.'.id_usuario', 'left');

		//$this->db->join('detallespedidos', 'detallespedidos.idpedido = '.$this->tabla.'.id', 'left');
		

		//if(isset($objeto['activo'])) $this->db->where('activo', '1');

		//if(isset($objeto['destacado'])) $this->db->where('destacado', '1');

		if(isset($objeto['usuarios'])) $this->db->where('usuarios.idusuario', $objeto['usuarios']);

		//if(isset($objeto['detallespedidos'])) $this->db->where('detallespedidos.iddetalle', $objeto['detallespedidos']);


		//if(isset($objeto['marca'])) $this->db->where('marcas.slug', $objeto['marca']);

		//if(isset($objeto['bnombre'])) $this->db->where("(archivos.codigo LIKE '%".$objeto['bnombre']."%' OR marcas.nombre LIKE '%".$objeto['bnombre']."%' OR producto.nombre LIKE '%".$objeto['bnombre']."%' OR categorias_productos.nombre LIKE '%".$objeto['bnombre']."%' OR producto.descripcion LIKE '%".$objeto['bnombre']."%')");




		$tmp = $q->get()->result();



		$ret['num_rows'] = $tmp[0]->count;



		return $ret;

	}



	function getCategoria($tipo){



		$this->db->select($this->tabla.'.*, usuarios.nombre as usuario, usuarios.apellido as apellido, usuarios.telefono as telefono, usuarios.email as email, detallespedidos.iddetalle as detalle, detallespedidos.idpedido as detalle_pedido, detallespedidos.idarchivo as detalle_archivo');
		$this->db->join('usuarios', 'usuarios.idusuario = '.$this->tabla.'.id_usuario', 'left');
		$this->db->join('detallespedidos', 'detallespedidos.idpedido = '.$this->tabla.'.id', 'left');

		$this->db->distinct();

		$this->db->where('tipo',$tipo);

		$this->db->order_by("nombre", "asc");

		$ret['rows'] = $this->db->get($this->tabla)->result();

		$this->db->last_query();

		return $ret;

	}



	function getImagenPrincipal($id){

		$this->db->select('nombre');

		$this->db->where('sub_id',$id);

		$this->db->where('principal',1);

		return $this->db->get('imagenes')->result();

	}



	function getImagenes($id){

		$this->db->select('nombre');

		$this->db->where('sub_id',$id);

		//$this->db->where('principal',2);

		return $this->db->get('imagenes')->result();

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