<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Tart_Filter_Entry_Search definition
 *
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
abstract class Kohana_Tart_Filter_Entry_Search extends Tart_Filter_Entry {

	protected $_label = 'Search';

	public function default_callback()
	{
		return function($collection, $value, $name) {
			$collection->where(':name_key', 'LIKE', "%{$value}%");
			return "named '{$value}'";
		};
	}

	public function render()
	{
		return $this->parent()->form()->row('input', $this->name(), array('label' => $this->label()), array('class' => 'search', 'tabindex' => $this->tabindex()));
	}
}