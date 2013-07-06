<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Tart_Group_Item definition
 *
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
abstract class Kohana_Tart_Group_Item extends Tart_Interface {

	protected $_label;
	protected $_name;
	protected $_callback;

	public function defaults($name, $label, $parent)
	{
		if ( ! $this->_name)
		{
			$this->_name = $name;
		}

		if ( ! $this->_label)
		{
			$this->_label = $label;
		}

		if ( ! $this->parent())
		{
			$this->_parent = $parent;
		}

		return $this;
	}

	public function label($label = NULL)
	{
		if ($label !== NULL)
		{
			$this->_label = $label;
			return $this;
		}
		return $this->_label;
	}

	public function name($name = NULL)
	{
		if ($name !== NULL)
		{
			$this->_name = $name;
			return $this;
		}
		return $this->_name;
	}

	public function callback($callback = NULL)
	{
		if ($callback !== NULL)
		{
			$this->_callback = $callback;
			return $this;
		}

		if ($this->_callback === NULL)
		{
			$this->_callback = $this->default_callback();
		}

		return $this->_callback;
	}

	public function default_callback()
	{
		return FALSE;
	}
}