<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Tart_Filter_Entry_Radios definition
 *
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
abstract class Kohana_Tart_Filter_Entry_Radios extends Tart_Filter_Entry {

	protected $_all = 'All';
	
	public function all($all = NULL)
	{
		if ($all !== NULL)
		{
			$this->_all = $all;
			return $this;
		}
		return $this->_all;
	}


	public function render()
	{
		return Tart::form($this, function($h, $self){
			$h('label', $self->label());

			$h->add($self->parent()->form()->radios($self->name(), array('choices' => $self->params(), 'include_blank' => $self->all())));
		});
	}

}