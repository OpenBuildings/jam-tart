<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Tart_Request definition
 *
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
abstract class Kohana_Tart_Request {

	/**
	 * Covert $_FILES array to the format, used by $_POST for multiple values
	 *
	 * @param array $tainted_files 
	 * @return array
	 * @author Ivan Kerin
	 */
	public static function fix_files(array $tainted_files)
	{
		$files = array();
		foreach ($tainted_files as $key => $data)
		{
			$files[$key] = Tart_Request::fix_php_files_array($data);
		}

		return $files;
	}

	/**
	 * @param array $data 
	 * @return array
	 * @author Ivan Kerin
	 */
	protected static function fix_php_files_array($data)
	{
		$file_keys = array('error', 'name', 'size', 'tmp_name', 'type');
		$keys = array_keys($data);
		sort($keys);

		if ($file_keys != $keys OR ! isset($data['name']) OR ! is_array($data['name']))
		{
			return $data;
		}

		$files = $data;
		foreach ($file_keys as $k)
		{
			unset($files[$k]);
		}
		foreach (array_keys($data['name']) as $key)
		{
			$files[$key] = Tart_Request::fix_php_files_array(array(
				'error'    => $data['error'][$key],
				'name'     => $data['name'][$key],
				'type'     => $data['type'][$key],
				'tmp_name' => $data['tmp_name'][$key],
				'size'     => $data['size'][$key],
			));
		}

		return $files;
	}

	public static function post($post, $file)
	{
		$files = Tart_Request::fix_files($file);

		return Tart_Request::arr_merge($files, $post);
	}

	public static function arr_merge(array $arr1, array $arr2)
	{
		$total = func_num_args();

		$result = array();
		for ($i = 0; $i < $total; $i++)
		{
			foreach (func_get_arg($i) as $key => $val)
			{
				if (isset($result[$key]))
				{
					if (is_array($val))
					{
						// Arrays are merged recursively
						$result[$key] = Tart_Request::arr_merge($result[$key], $val);
					}
					elseif (is_int($key))
					{
						// Indexed arrays are appended
						array_push($result, $val);
					}
					else
					{
						// Associative arrays are replaced
						$result[$key] = $val;
					}
				}
				else
				{
					// New values are added
					$result[$key] = $val;
				}
			}
		}

		return $result;
	}

	public static function modified_params(array $modified, array $expected = NULL)
	{
		if ($expected)
		{
			$modified = Jam::permit($expected, $modified);
		}

		$modified = array_filter($modified);

		return array_map(function($param) { 
			return $param === '__clear' ? FALSE : (is_array($param) ? Tart_Request::modified_params($param) : $param);
		}, $modified);
	}

	public static function to_modifications(array $modified)
	{
		foreach ($modified as $key => & $value) 
		{
			$value = Inflector::humanize($key).(is_array($value) ? ': '.Tart_Request::to_modifications($value) : ($value ? ' set to "'.$value.'"' : ' cleared'));
		}
		return Tart::to_sentence(array_values($modified));
	}
}