<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Cajas diarias: listado de cierres, detalle de cada cierre, eliminar y reabrir.
 */
class Cajasdiarias extends MY_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('Cajasdiarias_model', 'cajas');
	}

	function index()
	{
		$filtros = array(
			'sucursal' => (int) $this->input->get('sucursal'),
			'desde'    => p_fecha_valida($this->input->get('desde'), ''),
			'hasta'    => p_fecha_valida($this->input->get('hasta'), ''),
		);

		$pag = $this->paginacion($this->cajas->contar($filtros), 30);
		$this->data['filtros']    = $filtros;
		$this->data['pag']        = $pag;
		$this->data['cajas']      = $this->cajas->listar($filtros, $pag['por_pagina'], $pag['offset']);
		$this->data['totales']    = $this->cajas->totales($filtros);
		$this->data['sucursales'] = $this->sucursales();

		$this->mostrar('admin/cajasdiarias/list', 'Cajas diarias', 'cajas');
	}

	/** Detalle de un cierre: resumen, medios de pago, impresiones y ventas del dia. */
	function ver($id = 0)
	{
		$caja = $this->cajas->obtener($id);
		if ( ! $caja)
		{
			p_flash('Ese cierre de caja no existe.', 'error');
			redirect('admin/cajasdiarias');
		}

		$sucursales = $this->sucursales();
		$this->data['caja'] = $caja;
		$this->data['sucursal'] = isset($sucursales[(int) $caja->id_usuario]) ? $sucursales[(int) $caja->id_usuario] : NULL;
		$this->data['ventas'] = $this->cajas->ventas_del_dia($caja->id_usuario, $caja->fecha);

		$titulo = 'Cierre del '.p_fecha($caja->fecha);
		$this->data['acciones'] = array(
			array('url' => site_url('admin/cajasdiarias?sucursal='.(int) $caja->id_usuario), 'texto' => 'Volver al listado', 'icono' => 'ti-arrow-left'),
		);
		$this->mostrar('admin/cajasdiarias/ver', $titulo, 'cajas');
	}

	/** Enlaces viejos: /admin/cajasdiarias/edit/{id} */
	function edit($id = 0)
	{
		redirect('admin/cajasdiarias/ver/'.(int) $id);
	}

	function eliminar()
	{
		$this->solo_post();
		$id = (int) $this->input->post('id');
		if ($this->cajas->eliminar($id))
			p_flash('Se eliminó el cierre de caja.');
		else
			p_flash('No se pudo eliminar el cierre.', 'error');
		redirect('admin/cajasdiarias');
	}

	/**
	 * Reabre la caja de una sucursal (para que pueda volver a vender hoy).
	 * El cierre guardado no se borra: al volver a cerrar se actualiza.
	 */
	function reabrir()
	{
		$this->solo_post();
		$sucursal = (int) $this->input->post('sucursal');
		$this->db->set('caja_cerrada', 2)->where('idusuario', $sucursal)->update('usuarios');
		p_flash('Se reabrió la caja de la sucursal.');
		// Solo se vuelve a paginas del panel
		$volver = (string) $this->input->post('volver');
		redirect(preg_match('#^admin(/[A-Za-z0-9_/-]*)?(\?[A-Za-z0-9_=&%-]*)?$#', $volver) ? $volver : 'admin/cajasdiarias');
	}
}
