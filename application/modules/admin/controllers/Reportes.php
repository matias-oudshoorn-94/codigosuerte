<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Reportes de ventas. Todos comparten los filtros de periodo y sucursal:
 *   ?desde=YYYY-MM-DD&hasta=YYYY-MM-DD&sucursal=ID   (o ?periodo=este_mes, ver atajos())
 * Los que tienen tabla aceptan &csv=1 para descargarla.
 */
class Reportes extends MY_Controller
{
	/** Rango maximo para agrupar por dia en los graficos. */
	const DIAS_MAX_POR_DIA = 92;

	function __construct()
	{
		parent::__construct();
		$this->load->model('Reportes_model', 'reportes');
		$this->data['graficos'] = TRUE;
	}

	// ---------- Filtros comunes ----------

	/** Atajos de periodo: clave => [texto, desde, hasta] */
	private function atajos()
	{
		$hoy = date('Y-m-d');
		return array(
			'hoy'         => array('Hoy', $hoy, $hoy),
			'7_dias'      => array('Últimos 7 días', date('Y-m-d', strtotime('-6 days')), $hoy),
			'30_dias'     => array('Últimos 30 días', date('Y-m-d', strtotime('-29 days')), $hoy),
			'este_mes'    => array('Este mes', date('Y-m-01'), $hoy),
			'mes_pasado'  => array('Mes pasado', date('Y-m-01', strtotime('first day of last month')), date('Y-m-t', strtotime('last day of last month'))),
			'90_dias'     => array('Últimos 90 días', date('Y-m-d', strtotime('-89 days')), $hoy),
			'este_anio'   => array('Este año', date('Y-01-01'), $hoy),
			'anio_pasado' => array('Año pasado', (date('Y') - 1).'-01-01', (date('Y') - 1).'-12-31'),
		);
	}

	private function filtros($defecto = 'este_mes')
	{
		$atajos = $this->atajos();
		$periodo = $this->input->get('periodo');
		$desde = $this->input->get('desde');
		$hasta = $this->input->get('hasta');

		if ($periodo && isset($atajos[$periodo]))
		{
			list(, $desde, $hasta) = $atajos[$periodo];
		}
		elseif ( ! $desde && ! $hasta)
		{
			$periodo = $defecto;
			list(, $desde, $hasta) = $atajos[$defecto];
		}
		else
		{
			$periodo = '';
		}
		$desde = p_fecha_valida($desde, date('Y-m-01'));
		$hasta = p_fecha_valida($hasta, date('Y-m-d'));
		if ($desde > $hasta) list($desde, $hasta) = array($hasta, $desde);

		$dias = (int) ((strtotime($hasta) - strtotime($desde)) / 86400) + 1;

		// Periodo anterior de igual largo, para comparar
		$hasta_anterior = date('Y-m-d', strtotime($desde.' -1 day'));
		$desde_anterior = date('Y-m-d', strtotime($hasta_anterior.' -'.($dias - 1).' days'));

		$sucursales = $this->sucursales();
		$sucursal = (int) $this->input->get('sucursal');
		if ( ! isset($sucursales[$sucursal])) $sucursal = 0;

		$f = array(
			'periodo'        => $periodo,
			'desde'          => $desde,
			'hasta'          => $hasta,
			'dias'           => $dias,
			'agrupar'        => $dias > self::DIAS_MAX_POR_DIA ? 'mes' : 'dia',
			'desde_anterior' => $desde_anterior,
			'hasta_anterior' => $hasta_anterior,
			'sucursal'       => $sucursal,
		);
		$this->data['f'] = $f;
		$this->data['atajos'] = $atajos;
		$this->data['sucursales'] = $sucursales;
		$this->data['nombre_sucursal'] = function ($id) use ($sucursales) {
			return isset($sucursales[(int) $id]) ? $sucursales[(int) $id]->etiqueta : 'Sucursal #'.(int) $id.' (eliminada)';
		};
		return $f;
	}

	private function mostrar_reporte($vista, $titulo, $pestana)
	{
		$this->data['pestana'] = $pestana;
		$this->mostrar('admin/reportes/'.$vista, $titulo, 'reportes');
	}

	/** Descarga una tabla como CSV (separado por ; para que Excel en español lo abra bien). */
	private function csv($nombre, $encabezados, $filas)
	{
		$this->output->set_content_type('text/csv', 'UTF-8')
			->set_header('Content-Disposition: attachment; filename="'.$nombre.'-'.date('Y-m-d').'.csv"');
		$salida = fopen('php://temp', 'r+');
		fwrite($salida, "\xEF\xBB\xBF"); // BOM para que Excel respete los acentos
		fputcsv($salida, $encabezados, ';');
		foreach ($filas as $fila)
		{
			fputcsv($salida, array_map(function ($v) { return is_float($v) ? str_replace('.', ',', (string) round($v, 2)) : $v; }, $fila), ';');
		}
		rewind($salida);
		$this->output->set_output(stream_get_contents($salida));
		fclose($salida);
	}

	private function quiere_csv()
	{
		return (bool) $this->input->get('csv');
	}

	// ---------- Reportes ----------

	/** Resumen del periodo. */
	function index()
	{
		$f = $this->filtros();
		$this->data['actual']   = $this->reportes->resumen($f['desde'], $f['hasta'], $f['sucursal']);
		$this->data['anterior'] = $this->reportes->resumen($f['desde_anterior'], $f['hasta_anterior'], $f['sucursal']);
		$this->data['serie']    = $this->reportes->serie($f['desde'], $f['hasta'], $f['sucursal'], $f['agrupar']);
		$this->data['medios']   = $this->reportes->por_medio($f['desde'], $f['hasta'], $f['sucursal']);
		$this->data['por_sucursal'] = $f['sucursal'] ? array() : $this->reportes->por_sucursal($f['desde'], $f['hasta']);
		$this->data['productos'] = $this->reportes->productos($f['desde'], $f['hasta'], $f['sucursal'], 5);
		$this->mostrar_reporte('resumen', 'Reportes', 'resumen');
	}

	/** Ventas por dia o por mes, con el desglose por medio de pago. */
	function ventas()
	{
		$f = $this->filtros();
		$agrupar = in_array($this->input->get('agrupar'), array('dia', 'mes'), TRUE) ? $this->input->get('agrupar') : $f['agrupar'];
		if ($agrupar === 'dia' && $f['dias'] > 400) $agrupar = 'mes'; // demasiados dias para un grafico legible
		$serie = $this->reportes->serie($f['desde'], $f['hasta'], $f['sucursal'], $agrupar);

		if ($this->quiere_csv())
		{
			$filas = array();
			foreach ($serie as $s)
			{
				$filas[] = array($s->periodo, (int) $s->ventas, (float) $s->total, (float) $s->medio_0, (float) $s->medio_1, (float) $s->medio_2, (float) $s->medio_3, $s->ventas ? (float) ($s->total / $s->ventas) : 0.0);
			}
			return $this->csv('ventas', array($agrupar === 'mes' ? 'Mes' : 'Fecha', 'Ventas', 'Total', 'Efectivo', 'Tarjeta', 'Medio electrónico', 'Transferencia/QR', 'Ticket promedio'), $filas);
		}
		$this->data['agrupar'] = $agrupar;
		$this->data['serie'] = $serie;
		$this->mostrar_reporte('ventas', 'Reporte de ventas', 'ventas');
	}

	/** Mes a mes de un año, comparado con el año anterior. */
	function mensual()
	{
		$this->filtros();
		$anio = (int) $this->input->get('anio') ?: (int) date('Y');
		$anio = max(2015, min((int) date('Y'), $anio));
		$sucursal = $this->data['f']['sucursal'];

		$actual = $this->reportes->serie($anio.'-01-01', $anio.'-12-31', $sucursal, 'mes');
		$anterior = $this->reportes->serie(($anio - 1).'-01-01', ($anio - 1).'-12-31', $sucursal, 'mes');

		if ($this->quiere_csv())
		{
			$meses = p_meses();
			$filas = array();
			foreach ($actual as $i => $m)
			{
				$v = p_variacion($m->total, $anterior[$i]->total);
				$filas[] = array(ucfirst($meses[$i + 1]), (int) $m->ventas, (float) $m->total, (int) $anterior[$i]->ventas, (float) $anterior[$i]->total, $v === NULL ? '' : (float) $v);
			}
			return $this->csv('mensual-'.$anio, array('Mes', 'Ventas '.$anio, 'Total '.$anio, 'Ventas '.($anio - 1), 'Total '.($anio - 1), 'Variación %'), $filas);
		}
		$this->data['anio'] = $anio;
		$this->data['actual'] = $actual;
		$this->data['anterior'] = $anterior;
		$this->mostrar_reporte('mensual', 'Reporte mensual', 'mensual');
	}

	/** Comparacion entre sucursales. */
	function sucursales()
	{
		$f = $this->filtros();
		$totales = $this->reportes->por_sucursal($f['desde'], $f['hasta']);
		$anteriores = array();
		foreach ($this->reportes->por_sucursal($f['desde_anterior'], $f['hasta_anterior']) as $a) $anteriores[(int) $a->sucursal] = $a;

		$nombre = $this->data['nombre_sucursal'];
		if ($this->quiere_csv())
		{
			$filas = array();
			foreach ($totales as $t)
			{
				$prev = isset($anteriores[(int) $t->sucursal]) ? $anteriores[(int) $t->sucursal]->total : 0;
				$v = p_variacion($t->total, $prev);
				$filas[] = array($nombre($t->sucursal), (int) $t->ventas, (float) $t->total, $t->ventas ? (float) ($t->total / $t->ventas) : 0.0, (int) $t->dias, $v === NULL ? '' : (float) $v);
			}
			return $this->csv('sucursales', array('Sucursal', 'Ventas', 'Total', 'Ticket promedio', 'Días con ventas', 'Variación % vs período anterior'), $filas);
		}

		// Serie en el tiempo: las 3 sucursales que mas vendieron; el resto se suma en "Otras"
		$principales = array_slice(array_map(function ($t) { return (int) $t->sucursal; }, $totales), 0, 3);
		$hay_otras = count($totales) > 3;
		$periodos = array();
		foreach ($this->reportes->serie($f['desde'], $f['hasta'], 0, $f['agrupar']) as $p) $periodos[$p->periodo] = array();
		foreach ($this->reportes->serie_por_sucursal($f['desde'], $f['hasta'], $f['agrupar']) as $fila)
		{
			$clave = in_array((int) $fila->sucursal, $principales, TRUE) ? (int) $fila->sucursal : 'otras';
			if ( ! isset($periodos[$fila->periodo][$clave])) $periodos[$fila->periodo][$clave] = 0;
			$periodos[$fila->periodo][$clave] += $fila->total;
		}

		$this->data['totales'] = $totales;
		$this->data['anteriores'] = $anteriores;
		$this->data['principales'] = $principales;
		$this->data['hay_otras'] = $hay_otras;
		$this->data['periodos'] = $periodos;
		$this->mostrar_reporte('sucursales', 'Reporte por sucursal', 'sucursales');
	}

	/** Productos mas vendidos. */
	function productos()
	{
		$f = $this->filtros();
		$productos = $this->reportes->productos($f['desde'], $f['hasta'], $f['sucursal'], 100);

		if ($this->quiere_csv())
		{
			$filas = array();
			foreach ($productos as $i => $p)
			{
				$filas[] = array($i + 1, $p->nombre !== NULL ? $p->nombre : 'Producto eliminado (#'.$p->id.')', $p->codigo, (int) $p->unidades, (int) $p->ventas, (float) $p->precio_final, (float) $p->importe_estimado);
			}
			return $this->csv('productos', array('Puesto', 'Producto', 'Código', 'Unidades', 'Ventas en que aparece', 'Precio actual', 'Importe estimado'), $filas);
		}
		$this->data['productos'] = $productos;
		$this->mostrar_reporte('productos', 'Productos más vendidos', 'productos');
	}

	/** Cuándo se vende: dia de la semana y hora. */
	function horarios()
	{
		$f = $this->filtros('90_dias');
		$matriz = array();
		foreach ($this->reportes->por_dia_y_hora($f['desde'], $f['hasta'], $f['sucursal']) as $fila)
		{
			$matriz[(int) $fila->dia][(int) $fila->hora] = $fila;
		}
		$this->data['matriz'] = $matriz;
		$this->mostrar_reporte('horarios', 'Días y horarios de venta', 'horarios');
	}

	/** Impresiones declaradas vs vendidas y desperdicio. */
	function impresiones()
	{
		$f = $this->filtros();
		$dias = $this->reportes->impresiones($f['desde'], $f['hasta'], $f['sucursal']);
		$impresoras = $this->reportes->impresiones_por_impresora($f['desde'], $f['hasta'], $f['sucursal']);

		if ($this->quiere_csv())
		{
			$filas = array();
			foreach ($dias as $d)
			{
				$desperdicio = $d->reales > 0 ? ($d->reales - $d->vendidas) / $d->reales * 100 : 0;
				$filas[] = array($d->fecha, (int) $d->reales, (int) $d->vendidas, (int) ($d->reales - $d->vendidas), (float) $desperdicio);
			}
			return $this->csv('impresiones', array('Fecha', 'Declaradas', 'Vendidas', 'Diferencia', 'Desperdicio %'), $filas);
		}
		$this->data['dias'] = $dias;
		$this->data['impresoras'] = $impresoras;
		$this->mostrar_reporte('impresiones', 'Impresiones y desperdicio', 'impresiones');
	}

	// Enlaces viejos
	function parcial()     { redirect('admin/reportes/ventas'); }
	function grafico()     { redirect('admin/reportes/ventas'); }
	function comparativo() { redirect('admin/reportes/sucursales'); }
}
