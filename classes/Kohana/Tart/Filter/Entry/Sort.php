<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Tart_Filter_Entry_Sort definition
 *
 * @package Jam tart
 * @author Yasen Yanev
 * @copyright  (c) 2014 Despark Ltd.
 */
abstract class Kohana_Tart_Filter_Entry_Sort extends Tart_Filter_Entry {

	public function default_callback()
	{
		return function($collection, $value, $entry) {

			list($column, $direction) = explode(':', $value);

			if ($field = $collection->meta()->field($column))
			{
				$collection->order_by($column, $direction);

				return __('Sort by :column :direction', array(':column' => $column, ':direction' => __($direction)));
			}

			return NULL;
		};
	}

	public function render()
	{
		return $this->parent()->form()->hidden($this->name());
	}

}