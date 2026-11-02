<?php
class productos_model extends CI_Model{
	private $tabla = 'productos';
	private $id = 'id';
	private $busqueda = 'nombre';
	private $order =  array('orden');

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
		if(isset($objeto['search'])) $this->db->like($this->busqueda, $objeto['search']);
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
		if(isset($objeto['search'])) $this->db->like($this->busqueda, $objeto['search']);

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

	// ---------- Panel de administracion ----------

	/** Filtros del listado: q (nombre o codigo), repetidos (solo codigos repetidos). */
	private function filtrar_panel($q, $repetidos)
	{
		$q = trim((string) $q);
		if ($q !== '')
		{
			$this->db->group_start()->like('p.nombre', $q)->or_like('p.codigo', $q)->group_end();
		}
		if ($repetidos)
		{
			$this->db->where("p.codigo IN (SELECT codigo FROM (SELECT codigo FROM productos WHERE codigo <> '' GROUP BY codigo HAVING COUNT(*) > 1) r)", NULL, FALSE);
		}
	}

	public function contar_panel($q, $repetidos)
	{
		$this->db->from('productos p');
		$this->filtrar_panel($q, $repetidos);
		return $this->db->count_all_results();
	}

	/**
	 * Productos con stock total (suma de sucursales) y si el codigo esta repetido.
	 * $orden: columna permitida; $direccion: asc|desc
	 */
	public function listar_panel($q, $repetidos, $orden, $direccion, $limite, $offset)
	{
		$columnas = array('nombre' => 'p.nombre', 'codigo' => 'p.codigo', 'precio' => 'p.precio', 'precio_final' => 'p.precio_final', 'stock' => 'stock_total', 'id' => 'p.id');
		$columna = isset($columnas[$orden]) ? $columnas[$orden] : 'p.nombre';
		$this->db->select("p.*, COALESCE((SELECT SUM(s.stock) FROM stock_sucursal s WHERE s.producto_id = p.id), 0) AS stock_total,
			(SELECT COUNT(*) FROM productos p2 WHERE p2.codigo = p.codigo AND p2.codigo <> '' AND p2.id <> p.id) AS repetidos", FALSE)
			->from('productos p');
		$this->filtrar_panel($q, $repetidos);
		if ($repetidos) $this->db->order_by('p.codigo', 'ASC');
		return $this->db->order_by($columna, $direccion === 'desc' ? 'DESC' : 'ASC')
			->order_by('p.id', 'ASC')
			->limit($limite, $offset)->get()->result();
	}

	public function obtener($id)
	{
		return $this->db->where('id', (int) $id)->get('productos')->row();
	}

	/** Otros productos con el mismo codigo. */
	public function mismo_codigo($codigo, $excepto_id = 0)
	{
		if (trim((string) $codigo) === '') return array();
		return $this->db->select('id, nombre')->where('codigo', $codigo)->where('id !=', (int) $excepto_id)
			->order_by('nombre', 'ASC')->get('productos')->result();
	}

	/** Stock por sucursal: idusuario => stock */
	public function stock_por_sucursal($id)
	{
		$stock = array();
		foreach ($this->db->where('producto_id', (int) $id)->get('stock_sucursal')->result() as $fila) $stock[(int) $fila->idusuario] = (int) $fila->stock;
		return $stock;
	}

	public function guardar($id, $datos)
	{
		if ($id)
		{
			$this->db->where('id', (int) $id)->update('productos', $datos);
			return (int) $id;
		}
		$this->db->insert('productos', $datos);
		return (int) $this->db->insert_id();
	}

	public function veces_vendido($id)
	{
		return $this->db->where('id_articu', (int) $id)->count_all_results('pedidosdetalles');
	}

	public function eliminar($id)
	{
		$this->db->where('producto_id', (int) $id)->delete('stock_sucursal');
		$this->db->where('id', (int) $id)->delete('productos');
	}
}
