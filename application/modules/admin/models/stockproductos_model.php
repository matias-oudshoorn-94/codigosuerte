<?php
class stockproductos_model extends CI_Model {
    private $tabla = 'stock_sucursal';
    private $id = 'id';

    function __construct() {
        parent::__construct();
    }

    function get_by_producto($producto_id) {
    $this->db->select('stock_sucursal.*, usuarios.nombre as sucursal');
    $this->db->from($this->tabla);
    $this->db->join('usuarios', 'usuarios.idusuario = stock_sucursal.idusuario');
    $this->db->where('stock_sucursal.producto_id', $producto_id);
    return $this->db->get()->result();
}

    function save_or_update($producto_id, $idusuario, $stock) {
        $this->db->where('producto_id', $producto_id);
        $this->db->where('idusuario', $idusuario);
        $query = $this->db->get($this->tabla);
        if ($query->num_rows() > 0) {
            $this->db->where('producto_id', $producto_id);
            $this->db->where('idusuario', $idusuario);
            $this->db->update($this->tabla, ['stock' => $stock]);
        } else {
            $this->db->insert($this->tabla, [
                'producto_id' => $producto_id,
                'idusuario' => $idusuario,
                'stock' => $stock
            ]);
        }
    }
}