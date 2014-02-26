<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Tart_Group definition
 *
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
abstract class Kohana_Tart_Group extends Tart_Interface_Collection {

	protected $_items = array();

	public function items($key = NULL, $value = NULL)
	{
		if ($key === NULL)
			return $this->_items;

		if (is_array($key))
		{
			$this->_items = $key;
			foreach ($this->_items as $key => $value)
			{
				if ( ! ($value instanceof Tart_Group_Item))
					throw new Kohana_Exception('Item :name must be instance of class Tart_Group_Item', array(':name' => $key));

				$label = __(ucfirst(Inflector::humanize($key)));
				$value->defaults($key, $label, $this);
			}
		}
		else
		{
			if ($value === NULL)
				return Arr::get($this->_items, $key);

			if ( ! ($value instanceof Tart_Group_Item))
				throw new Kohana_Exception('Item :name must be instance of class Tart_Group_Item', array(':name' => $key));

			$label = __(ucfirst(Inflector::humanize($key)));
			$this->_items[$key] = $value->defaults($key, $label, $this);
		}

		return $this;
	}

}