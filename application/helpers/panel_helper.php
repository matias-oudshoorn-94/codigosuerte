<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * Funciones de formato para el panel de administracion.
 */

if ( ! function_exists('p_e'))
{
	/** Escapa texto para HTML. */
	function p_e($texto)
	{
		return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
	}
}

if ( ! function_exists('p_pesos'))
{
	/** $ 12.345 (sin decimales, como en el resto del sistema). */
	function p_pesos($valor, $decimales = 0)
	{
		return '$ '.number_format((float) $valor, $decimales, ',', '.');
	}
}

if ( ! function_exists('p_numero'))
{
	function p_numero($valor, $decimales = 0)
	{
		return number_format((float) $valor, $decimales, ',', '.');
	}
}

if ( ! function_exists('p_porcentaje'))
{
	function p_porcentaje($valor, $decimales = 1)
	{
		return number_format((float) $valor, $decimales, ',', '.').'%';
	}
}

if ( ! function_exists('p_variacion'))
{
	/**
	 * Variacion porcentual entre dos valores, o NULL si no se puede calcular.
	 */
	function p_variacion($actual, $anterior)
	{
		$anterior = (float) $anterior;
		if ($anterior == 0) return NULL;
		return ((float) $actual - $anterior) / $anterior * 100;
	}
}

if ( ! function_exists('p_meses'))
{
	function p_meses()
	{
		return array(1 => 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre');
	}
}

if ( ! function_exists('p_fecha'))
{
	/** 2 nov 2026 */
	function p_fecha($fecha)
	{
		if ( ! $fecha || strpos($fecha, '0000-00-00') === 0) return '—';
		$t = strtotime($fecha);
		$cortos = array(1 => 'ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic');
		return date('j', $t).' '.$cortos[date('n', $t)].' '.date('Y', $t);
	}
}

if ( ! function_exists('p_fecha_larga'))
{
	/** lunes 2 de noviembre de 2026 */
	function p_fecha_larga($fecha)
	{
		$t = strtotime($fecha);
		$dias = array('domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado');
		$meses = p_meses();
		return $dias[date('w', $t)].' '.date('j', $t).' de '.$meses[date('n', $t)].' de '.date('Y', $t);
	}
}

if ( ! function_exists('p_hora'))
{
	function p_hora($fecha_hora)
	{
		return $fecha_hora ? date('H:i', strtotime($fecha_hora)) : '';
	}
}

if ( ! function_exists('p_nombre_sucursal'))
{
	/**
	 * Nombre legible de una sucursal (fila de usuarios), sin el punto suelto
	 * que tienen algunos registros en el apellido.
	 */
	function p_nombre_sucursal($fila)
	{
		$nombre = trim(trim($fila->nombre).' '.trim($fila->apellido), ' .');
		return $nombre !== '' ? $nombre : $fila->usuario;
	}
}

if ( ! function_exists('p_fecha_valida'))
{
	/** Devuelve la fecha si es YYYY-MM-DD valida, o $defecto. */
	function p_fecha_valida($fecha, $defecto)
	{
		$fecha = (string) $fecha;
		if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha))
		{
			list($a, $m, $d) = explode('-', $fecha);
			if (checkdate((int) $m, (int) $d, (int) $a)) return $fecha;
		}
		return $defecto;
	}
}

if ( ! function_exists('p_flash'))
{
	/** Guarda un mensaje para mostrar despues de redirigir. $tipo: ok | error */
	function p_flash($texto, $tipo = 'ok')
	{
		get_instance()->session->set_flashdata('panel_aviso', array('tipo' => $tipo, 'texto' => $texto));
	}
}

if ( ! function_exists('p_campo'))
{
	/**
	 * Campo de formulario con etiqueta, ayuda y error.
	 * $opciones: tipo, ayuda, clase (ej. 'ancho'), requerido, prefijo, attrs (texto extra)
	 */
	function p_campo($nombre, $etiqueta, $valores, $errores, $opciones = array())
	{
		$tipo = isset($opciones['tipo']) ? $opciones['tipo'] : 'text';
		$valor = isset($valores[$nombre]) ? $valores[$nombre] : '';
		$error = isset($errores[$nombre]) ? $errores[$nombre] : '';
		$clase = 'pn-campo'.( ! empty($opciones['clase']) ? ' '.$opciones['clase'] : '').($error ? ' con-error' : '');
		$id = 'c-'.$nombre;
		$attrs = ' id="'.$id.'" name="'.p_e($nombre).'"'.( ! empty($opciones['requerido']) ? ' required' : '').(isset($opciones['attrs']) ? ' '.$opciones['attrs'] : '');
		if ($error) $attrs .= ' aria-invalid="true" aria-describedby="'.$id.'-error"';

		$html = '<div class="'.$clase.'"><label for="'.$id.'">'.p_e($etiqueta).( ! empty($opciones['requerido']) ? ' <span class="suave">*</span>' : '').'</label>';
		$input = '<input type="'.$tipo.'"'.$attrs.' value="'.($tipo === 'password' ? '' : p_e($valor)).'">';
		if ( ! empty($opciones['prefijo'])) $input = '<div class="pn-prefijo"><span>'.p_e($opciones['prefijo']).'</span>'.$input.'</div>';
		$html .= $input;
		if ($error) $html .= '<div class="pn-error" id="'.$id.'-error">'.p_e($error).'</div>';
		if ( ! empty($opciones['ayuda'])) $html .= '<div class="pn-ayuda">'.p_e($opciones['ayuda']).'</div>';
		return $html.'</div>';
	}
}

if ( ! function_exists('p_opciones'))
{
	/** Grupo de opciones tipo boton (radio). $lista: valor => texto */
	function p_opciones($nombre, $etiqueta, $lista, $actual, $ayuda = '')
	{
		$html = '<div class="pn-campo"><label>'.p_e($etiqueta).'</label><div class="pn-opciones" role="radiogroup" aria-label="'.p_e($etiqueta).'">';
		foreach ($lista as $valor => $texto)
		{
			$html .= '<label class="pn-opcion"><input type="radio" name="'.p_e($nombre).'" value="'.p_e($valor).'"'.((string) $actual === (string) $valor ? ' checked' : '').'><span>'.p_e($texto).'</span></label>';
		}
		$html .= '</div>';
		if ($ayuda !== '') $html .= '<div class="pn-ayuda">'.p_e($ayuda).'</div>';
		return $html.'</div>';
	}
}

if ( ! function_exists('p_delta'))
{
	/**
	 * Etiqueta de variacion frente a un valor anterior: flecha + porcentaje (+ texto).
	 * Nunca solo color: lleva flecha y signo.
	 */
	function p_delta($actual, $anterior, $texto = '')
	{
		$v = p_variacion($actual, $anterior);
		if ($v === NULL) return '<span class="tenue">Sin datos para comparar</span>';
		$clase = abs($v) < 0.5 ? 'igual' : ($v > 0 ? 'sube' : 'baja');
		$flecha = $clase === 'sube' ? 'ti-arrow-up' : ($clase === 'baja' ? 'ti-arrow-down' : 'ti-minus');
		$signo = $clase === 'sube' ? '+' : ($clase === 'baja' ? '−' : '');
		return '<span class="pn-delta '.$clase.'"><i class="'.$flecha.'" aria-hidden="true"></i>'.$signo.p_porcentaje(abs($v)).'</span>'.($texto !== '' ? ' <span>'.$texto.'</span>' : '');
	}
}

if ( ! function_exists('p_rango'))
{
	/** "1 nov 2026 – 2 nov 2026" o una sola fecha si coinciden. */
	function p_rango($desde, $hasta)
	{
		return $desde === $hasta ? p_fecha($desde) : p_fecha($desde).' – '.p_fecha($hasta);
	}
}

if ( ! function_exists('p_etiqueta_periodo'))
{
	/** Etiqueta corta para ejes: dia "2/11", mes "nov 26". */
	function p_etiqueta_periodo($periodo, $agrupar)
	{
		if ($agrupar === 'mes')
		{
			$cortos = array(1 => 'ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic');
			return $cortos[(int) substr($periodo, 5, 2)].' '.substr($periodo, 2, 2);
		}
		return date('j/n', strtotime($periodo));
	}
}
