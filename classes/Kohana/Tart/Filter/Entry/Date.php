<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Tart_Filter_Entry_Date definition
 *
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
abstract class Kohana_Tart_Filter_Entry_Date extends Tart_Filter_Entry {

	public function render()
	{
		return $this->parent()->form()->row('input', $this->name(), array(), array('type' => 'date', 'tabindex' => $this->tabindex()));
	}

	public function default_callback()
	{
		return function($collection, $value, $entry) {
			$collection->where($entry->name(), '=', $value);
			return "on ".$value;
		};
	}
}