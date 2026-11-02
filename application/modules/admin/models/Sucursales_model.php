<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Sucursales (tabla usuarios) y sus impresoras, para el panel.
 * El login de sucursales sigue en Clientes_model (lo usa el sitio).
 */
class Sucursales_model extends CI_Model
{
	public function listar()
	{
		return $this->db->select('u.*, (SELECT COUNT(*) FROM impresoras i WHERE i.sub_id = u.idusuario) AS impresoras, (SELECT MAX(c.fecha) FROM cajas_diarias c WHERE c.id_usuario = u.idusuario) AS ultimo_cierre', FALSE)
			->from('usuarios u')
			->order_by('u.activo', 'ASC')->order_by('u.nombre', 'ASC')
			->get()->result();
	}

	public function obtener($id)
	{
		return $this->db->where('idusuario', (int) $id)->get('usuarios')->row();
	}

	public function usuario_en_uso($usuario, $excepto_id = 0)
	{
		return $this->db->where('usuario', $usuario)->where('idusuario !=', (int) $excepto_id)->count_all_results('usuarios') > 0;
	}

	public function guardar($id, $datos)
	{
		if ($id)
		{
			$this->db->where('idusuario', (int) $id)->update('usuarios', $datos);
			return (int) $id;
		}
		$this->db->insert('usuarios', $datos);
		return (int) $this->db->insert_id();
	}

	public function cantidad_ventas($id)
	{
		return $this->db->where('cod_clie', (string) (int) $id)->count_all_results('pedidos');
	}

	public function eliminar($id)
	{
		$this->db->where('idusuario', (int) $id)->delete('usuarios');
	}

	// ---------- Impresoras ----------

	public function impresoras($sucursal)
	{
		return $this->db->select('i.*, (SELECT MAX(d.fecha) FROM detalles_impresoras d WHERE d.id_impresora = i.codigo) AS ultima_declaracion', FALSE)
			->from('impresoras i')->where('i.sub_id', (int) $sucursal)
			->order_by('i.codigo', 'ASC')->get()->result();
	}

	public function impresora($id)
	{
		return $this->db->where('id', (int) $id)->get('impresoras')->row();
	}

	public function codigo_impresora_en_uso($codigo, $excepto_id = 0)
	{
		return $this->db->where('codigo', $codigo)->where('id !=', (int) $excepto_id)->count_all_results('impresoras') > 0;
	}

	public function guardar_impresora($id, $datos)
	{
		if ($id)
		{
			$anterior = $this->impresora($id);
			$this->db->where('id', (int) $id)->update('impresoras', $datos);
			// El historial se guarda por codigo: si cambia el codigo, se mueve con la impresora
			if ($anterior && isset($datos['codigo']) && $anterior->codigo !== $datos['codigo'])
			{
				$this->db->where('id_impresora', $anterior->codigo)->update('detalles_impresoras', array('id_impresora' => $datos['codigo']));
			}
			return (int) $id;
		}
		$this->db->insert('impresoras', $datos);
		return (int) $this->db->insert_id();
	}

	public function eliminar_impresora($id)
	{
		$this->db->where('id', (int) $id)->delete('impresoras');
	}

	public function historial_impresora($codigo)
	{
		return $this->db->select('fecha, impresiones')
			->where('id_impresora', $codigo)
			->order_by('fecha', 'DESC')->order_by('id', 'DESC')
			->get('detalles_impresoras')->result();
	}
}
