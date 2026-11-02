<?php
class clientes_model extends CI_Model{ /// ???????????? CLIENTES ???????????????????
	private $tabla = 'usuarios';
	private $id = 'idusuario';
	private $busqueda = 'nombre';
	private $order =  array('id');

	function __construct(){
		parent::__construct();
	}

	/**
	 * Login de sucursal. Devuelve ['rows' => [usuario], 'num_rows' => 0|1].
	 *
	 * Las claves se guardan encriptadas (password_hash). Si la clave de la
	 * sucursal todavia esta en texto plano (de antes de la migracion), se
	 * acepta una vez y se encripta en ese momento.
	 */
	function login()
	{
		$ret = array('rows' => array(), 'num_rows' => 0);

		$usuario = (string) $this->input->post('usuario');
		$clave   = (string) $this->input->post('clave');
		if ($usuario === '' || $clave === '')
		{
			return $ret;
		}

		$fila = $this->db->select('idusuario, clave, nombre, apellido, telefono, email, usuario, domicilio, localidad, caja_cerrada')
			->where('usuario', $usuario)
			->where('activo', 1)
			->limit(1)
			->get($this->tabla)
			->row();

		if ( ! $fila || ! $this->verificar_clave($fila, $clave))
		{
			return $ret;
		}

		unset($fila->clave); // no guardar la clave en la sesion
		$ret['rows'] = array($fila);
		$ret['num_rows'] = 1;
		return $ret;
	}

	/**
	 * Compara la clave ingresada con la guardada y, si hace falta,
	 * la encripta (o la vuelve a encriptar con el algoritmo actual).
	 */
	private function verificar_clave($fila, $clave)
	{
		$guardada = (string) $fila->clave;
		$info = password_get_info($guardada);

		if ($info['algo'] === NULL || $info['algo'] === 0)
		{
			// Clave vieja en texto plano
			if ( ! hash_equals($guardada, $clave))
			{
				return FALSE;
			}
		}
		elseif ( ! password_verify($clave, $guardada))
		{
			return FALSE;
		}

		if ($info['algo'] === NULL || $info['algo'] === 0 || password_needs_rehash($guardada, PASSWORD_BCRYPT))
		{
			$this->db->where($this->id, $fila->idusuario)
				->update($this->tabla, array('clave' => self::encriptar_clave($clave)));
		}

		return TRUE;
	}

	/**
	 * Encripta una clave para guardarla en usuarios.clave (varchar 60).
	 */
	public static function encriptar_clave($clave)
	{
		return password_hash((string) $clave, PASSWORD_BCRYPT);
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