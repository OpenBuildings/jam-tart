<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Stats_Widget definition
 *
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
abstract class Kohana_Stats_Widget {

	protected $_title = 'Widget';
	protected $_id;
	protected $_range;
	protected $_options = array();

	public function __construct($id, Tart_Date_Range $range = NULL)
	{
		$this->id($id);
		$this->range($range);
	}

	abstract function retrieve($from, $to);

	public function title($title = NULL)
	{
		if ($title !== NULL)
		{
			$this->_title = $title;
			return $this;
		}
		return $this->_title;
	}

	
	public function options($key = NULL, $value = NULL)
	{
		if ($key === NULL)
			return $this->_options;
	
		if (is_array($key))
		{
			$this->_options = $key;
		}
		else
		{
			if ($value === NULL)
				return Arr::get($this->_options, $key);
	
			$this->_options[$key] = $value;
		}
	
		return $this;
	}

	public function id($id = NULL)
	{
		if ($id !== NULL)
		{
			$this->_id = $id;
			return $this;
		}
		return $this->_id;
	}

	public function render()
	{
		$content = $this->retrieve($this->range() ? $this->range()->start() : NULL, $this->range() ? $this->range()->end() : NULL);

		return Tart::html($this, function ($h, $self) use ($content) {
			$h('div.caption', function($h, $self) use ($content) {
				$h('h4', $self->title());
				$h->add($content);
			});
		})->render();
	}

	public static function percent_diff($a, $b)
	{
		$a = $a instanceof Jam_Price ? $a->amount() : $a;
		$b = $b instanceof Jam_Price ? $b->amount() : $b;

		return ($b > 0) ? (($a - $b) / $b) * 100 : 0;
	}	

	public static function to_percent($a, $b)
	{
		$a = $a instanceof Jam_Price ? $a->amount() : $a;
		$b = $b instanceof Jam_Price ? $b->amount() : $b;

		return ($b > 0) ? ($a / $b ) * 100 : 0;
	}


	public function range(Tart_Date_Range $range = NULL)
	{
		if ($range !== NULL)
		{
			$this->_range = $range;
			return $this;
		}
		return $this->_range;
	}

	public function cache($mode = NULL)
	{
		if ($mode !== NULL)
		{
			$this->_cache = $mode;
			return $this;
		}

		return $this->_cache;
	}

	public function cache_key()
	{
		return substr(sha1(($this->range() ? $this->range()->render() : '').$this->id()), 0, 8);
	}

	public function __toString()
	{
		return $this->render();
	}
}
