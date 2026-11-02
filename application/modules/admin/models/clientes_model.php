<?php
class clientes_model extends CI_Model{ /// ???????????? CLIENTES ???????????????????
	private $tabla = 'usuarios';
	private $id = 'idusuario';
	private $busqueda = 'nombre';
	private $order =  array('id');

	function __construct(){
		parent::__construct();
	}

	function login()
	{

		$query = $this->db->select('idusuario, clave, nombre, apellido, telefono, email, usuario, domicilio, localidad, caja_cerrada');
		$this->db->where('usuario', $this->input->post('usuario'));
		//$this->db->where('clave', md5($this->input->post('clave')));
		$this->db->where('clave', $this->input->post('clave'));
		$this->db->where('activo', 1);


		$this->db->limit(1);

		$ret['rows'] = $this->db->get($this->tabla)->result();
		//echo $this->db->last_query();
		// count query
		$q = $this->db->select('COUNT(*) as count', FALSE)
			->from($this->tabla);
		$this->db->where('usuario', $this->input->post('usuario'));
		$this->db->where('clave', $this->input->post('clave'));
		$this->db->where('activo', 1);

		$this->db->limit(1);

		$tmp = $q->get()->result();
		//echo $this->db->last_query();

		$ret['num_rows'] = $tmp[0]->count;

		return $ret;
	}

	function registrar()
	{
		$query = $this->db->select('idusuario, clave, nombre, apellido, telefono, email, activo');
		$this->db->where('telefono', $this->input->post('telefono'));


		$this->db->limit(1);

		$ret['rows'] = $this->db->get($this->tabla)->result();
		//echo $this->db->last_query();
		// count query
		$q = $this->db->select('COUNT(*) as count', FALSE)
			->from($this->tabla);
		$this->db->where('telefono', $this->input->post('telefono'));

		$this->db->limit(1);

		$tmp = $q->get()->result();
		//echo $this->db->last_query();

		$ret['num_rows'] = $tmp[0]->count;

		if($ret['num_rows'] == 0 && $this->input->post('confir_pass') != $this->input->post('clave'))
		{
			$ret['num_rows'] = "pass";
		}

		return $ret;

	}

	function insertar() 
	{ 
		$arr = array();
		$arr['clave'] = md5($this->input->post('clave'));
		$arr['nombre'] = $this->input->post('nombre');
		$arr['apellido'] = $this->input->post('apellido');
		$arr['telefono'] = $this->input->post('telefono');
		$arr['email'] = $this->input->post('email');
	
		$query = "INSERT INTO usuarios (nombre,apellido,telefono,email,clave,activo) VALUES ('".$arr['nombre']."','".$arr['apellido']."','".$arr['telefono']."','".$arr['email']."','".$arr['clave']."',1)";
		$this->db->query($query);
	} 

	function getOne()
	{
		$query = $this->db->select('idusuario, clave, nombre, apellido, telefono, email, activo, localidad, domicilio'); 
		$this->db->where('email', $this->input->post('email'));
		$this->db->limit(1);
		
		$ret['rows'] = $this->db->get($this->tabla)->result();
		//echo $this->db->last_query();
		// count query
		$q = $this->db->select('COUNT(*) as count', FALSE)
			->from($this->tabla);
		$this->db->where('email', $this->input->post('email'));
		$this->db->limit(1);			
		
		$tmp = $q->get()->result();
		//echo $this->db->last_query();
		
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

	function updatePass($id_cliente, $pass)
	{
		//$query = "UPDATE usuarios SET clave='$pass' WHERE idusuario='$id_cliente' ";
		$query = "UPDATE usuarios SET clave = '".$pass."' WHERE idusuario = '".$id_cliente."' ";
		$this->db->query($query);
		echo $this->db->last_query();
	}

	function texto_aleatorio ($long = 5, $letras_min = true, $letras_max = true, $num = true) 
	{
		$salt = $letras_min?'abchefghknpqrstuvwxyz':'';
		$salt .= $letras_max?'ACDEFHKNPRSTUVWXYZ':'';
		$salt .= $num?(strlen($salt)?'2345679':'0123456789'):'';
		if (strlen($salt) == 0)
		{
			return '';
		}
		$i = 0;
		$str = '';

		srand((double)microtime()*1000000);
		
		while ($i < $long) 
		{
			$num = rand(0, strlen($salt)-1);
			$str .= substr($salt, $num, 1);
			$i++;
		}
		return $str;
	}


	function getAll($objeto = array())

	{

		//--Join--

		$this->db->select($this->tabla.'.*');

		//$this->db->join('categoria', 'categoria.id = '.$this->tabla.'.id_categoria', 'left');

		//--

		if(isset($objeto['sort_order']))

			$sort_order = ($objeto['sort_order'] == 'desc') ? 'desc' : 'asc';

		$sort_columns = $this->order;

		if(isset($objeto['sort_by']))

			$sort_by = (in_array($objeto['sort_by'], $sort_columns)) ? $objeto['sort_by'] : $this->id;



		// Qualification

		if(isset($objeto['search'])) $this->db->like($this->tabla.'.'.$this->busqueda, $objeto['search']);

		if(isset($objeto['id'])) $this->db->where($this->tabla.'.'.$this->id, $objeto['id']);

		//if(isset($objeto['tipo'])) $this->db->where($this->tabla.'.tipo', $objeto['tipo']);

		//if(isset($objeto['categoria'])) $this->db->where('categoria.slug', $objeto['categoria']);



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

		//$this->db->join('categoria', 'categoria.id = '.$this->tabla.'.id_categoria', 'left');

		if(isset($objeto['search'])) $this->db->like($this->tabla.'.'.$this->busqueda, $objeto['search']);

		//if(isset($objeto['tipo'])) $this->db->where($this->tabla.'.tipo', $objeto['tipo']);

		//if(isset($objeto['categoria'])) $this->db->where('categoria.slug', $objeto['categoria']);



		$tmp = $q->get()->result();



		$ret['num_rows'] = $tmp[0]->count;



		return $ret;

	}

	function sub_update($objeto)

	{

		$this->db->where('id', $objeto['id']);

		$this->db->update('impresoras', $objeto);

		return $this->db->affected_rows();

	}

	function saveImg($objeto)

	{

		$this->db->insert('impresoras', $objeto);

		return $this->db->insert_id();

	}


	function getAllList($objeto = array())

	{

		$this->db->where('sub_id', $objeto['sub_id']);

		$this->db->order_by("fecha", "desc");

		$ret['rows'] = $this->db->get('impresoras')->result();

		//echo $this->db->last_query();

		return $ret;

	}

	function getImg($objeto = array()){

		$this->db->where('id', $objeto['id']);

		$this->db->order_by("fecha", "desc");

		$ret['rows'] = $this->db->get('impresoras')->result();

		//echo $this->db->last_query();

		return $ret;



	}

	function getImagenPrincipal($id){

		$this->db->select('direccion');

		$this->db->where('sub_id',$id);

		$this->db->where('principal',1);

		return $this->db->get('impresoras')->result();

	}



	function getImagenes($id){

		$this->db->select('direccion');

		$this->db->where('sub_id',$id);

		//$this->db->where('principal',2);

		return $this->db->get('impresoras')->result();

	}

	function deleteImg($elementos, $sub_id)

	{

		$this->db->where_in('id', $elementos);

		$this->db->where('sub_id', $sub_id);

		$this->db->delete('impresoras');

	}
			
}