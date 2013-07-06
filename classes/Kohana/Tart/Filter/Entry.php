<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Tart_Filter_Entry definition
 *
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
abstract class Kohana_Tart_Filter_Entry extends Tart_Group_Item {

	protected $_active;
	protected $_params;
	protected $_tabindex;
	
	public function tabindex($tabindex = NULL)
	{
		if ($tabindex !== NULL)
		{
			$this->_tabindex = $tabindex;
			return $this;
		}
		return $this->_tabindex;
	}
	
	public function __construct($params, $callback)
	{
		$this->params($params);
		$this->callback($callback);
	}

	public function apply($collection, $value)
	{
		$callback = $this->callback();
		$this->_active = $callback($collection, $value, $this);
		return $this;
	}

	public function default_callback()
	{
		return function($collection, $value, $entry) {
			$collection->where($entry->name(), '=', $value);
			return $value;
		};
	}
	
	public function render()
	{
		return call_user_func($this->params(), $this->parent()->form(), $this->tabindex());
	}

	public function active()
	{
		return $this->_active;
	}

	public function params($params = NULL)
	{
		if ($params !== NULL)
		{
			$this->_params = $params;
			return $this;
		}
		return $this->_params;
	}
}