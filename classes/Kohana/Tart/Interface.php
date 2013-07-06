<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Tart_Interface definition
 *
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
abstract class Kohana_Tart_Interface {

	protected $_controller = NULL;
	protected $_parent = NULL;

	abstract public function render();
	
	public function parent(Tart_Interface $parent = NULL)
	{
		if ($parent !== NULL)
		{
			$this->_parent = $parent;
			return $this;
		}
		return $this->_parent;
	}
	
	public function controller($controller = NULL)
	{
		if ($controller !== NULL)
		{
			$this->_controller = $controller;
			return $this;
		}
		
		if ( ! $this->_controller AND $this->_parent)
		{
			$this->_controller = $this->_parent->controller();
		}

		return $this->_controller;
	}

	public function __toString()
	{
		return $this->render();
	}
}