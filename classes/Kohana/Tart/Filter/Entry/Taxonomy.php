<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Tart_Filter_Entry_Taxonomy definition
 *
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
abstract class Kohana_Tart_Filter_Entry_Taxonomy extends Tart_Filter_Entry{

	protected $_multiple = FALSE;
	
	public function multiple($multiple = NULL)
	{
		if ($multiple !== NULL)
		{
			$this->_multiple = $multiple;
			return $this;
		}
		return $this->_multiple;
	}

	public function render()
	{
		return $this->parent()->form()->row(
			'taxonomy', 
			$this->name(),
			array(
				'label' => $this->label(), 
				'vocabulary' => $this->params(),
				'include_blank' => TRUE
			), 
			array(
				'name' => $this->name().($this->multiple() ? '[]' : ''), 
				'multiple' => $this->multiple(), 
				'class' => 'chzn-select', 
				'tabindex' => $this->tabindex()
			)
		);
	}
}