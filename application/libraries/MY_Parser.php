<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Parser compatible con PHP 8.
 *
 * La libreria Template le pasa al parser todos los datos de la vista,
 * incluidos objetos de la base de datos (stdClass). En PHP 5.6 convertir
 * esos objetos a texto era un error recuperable que se ignoraba; en PHP 8
 * es una excepcion. Aca se omiten los valores que no se pueden convertir
 * a texto, igual que ocurria antes.
 */
class MY_Parser extends CI_Parser {

	protected function _parse($template, $data, $return = FALSE)
	{
		return parent::_parse($template, $this->_solo_convertibles($data), $return);
	}

	protected function _parse_pair($variable, $data, $string)
	{
		return parent::_parse_pair($variable, $this->_solo_convertibles($data), $string);
	}

	private function _solo_convertibles($data)
	{
		if ( ! is_array($data))
		{
			return $data;
		}

		foreach ($data as $key => $val)
		{
			if (is_array($val))
			{
				$data[$key] = $this->_solo_convertibles($val);
			}
			elseif (is_object($val) && ! method_exists($val, '__toString'))
			{
				unset($data[$key]);
			}
		}

		return $data;
	}
}
