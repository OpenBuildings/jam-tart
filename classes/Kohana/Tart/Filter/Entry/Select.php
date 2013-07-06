<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Tart_Filter_Entry_Select definition
 *
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
abstract class Kohana_Tart_Filter_Entry_Select extends Tart_Filter_Entry {

	protected $_multiple = NULL;
	
	public function multiple($multiple = NULL)
	{
		if ($multiple !== NULL)
		{
			$this->_multiple = $multiple;
			return $this;
		}
		return $this->_multiple;
	}

	public function default_callback()
	{
		return function($collection, $value, $entry) {
			$collection->where($entry->name(), 'IN', (array) $value);
			return join(', ', (array) $value);
		};
	}

	public function render()
	{
		return $this->parent()->form()->row(
			'select', 
			$this->name(), 
			array(
				'label' => $this->label(),
				'choices' => $this->params(), 
				'include_blank' => TRUE
			), 
			array(
				'multiple' => $this->multiple(), 
				'tabindex' => $this->tabindex(),
				'name' => $this->name().($this->multiple() ? '[]' : '')
			)
		);
	}
}