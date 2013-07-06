<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Tart_Interface_Collection definition
 *
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
abstract class Kohana_Tart_Interface_Collection extends Tart_Interface {

	protected $_selected = array();
	protected $_collection = array();
	
	public function selected($selected = NULL)
	{
		if ($selected !== NULL)
		{
			$this->_selected = $selected;
			return $this;
		}
		return $this->_selected;
	}

	public function collection($collection = NULL)
	{
		if ($collection !== NULL)
		{
			$this->_collection = $collection;
			return $this;
		}
		return $this->_collection;
	}
}