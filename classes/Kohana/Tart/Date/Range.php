<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * A method to deal with date ranges (start - end date)
 *
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
abstract class Kohana_Tart_Date_Range {

	protected $_period;
	protected $_start;
	protected $_end;

	public function __construct($start = NULL, $end = NULL, $period = NULL)
	{
		$this->start($start);
		$this->end($end);
		$this->period($period);
	}

	public function render()
	{
		$from = date('j M Y', $this->start());
		$to = date('j M Y', $this->end());

		return $from === $to ? "On {$from}" : "From {$from} - to {$to}";
	}

	public function url_query()
	{
		return URL::query(array(
			'start'  => date('Y-m-d', $this->start()),
			'end'    => date('Y-m-d', $this->end()),
			'period' => $this->period(),
		), FALSE);
	}

	public function __toString()
	{
		return $this->render();
	}
	
	public function end($end = NULL)
	{
		if ($end !== NULL)
		{
			$this->_end = strtotime($end);
			return $this;
		}
		return $this->_end;
	}

	public function start($start = NULL)
	{
		if ($start !== NULL)
		{
			$this->_start = strtotime($start);
			return $this;
		}
		return $this->_start;
	}
	
	public function previous_start()
	{
		return strtotime(date('Y-m-d', $this->start()).' - '.$this->period());
	}
	
	public function previous_end()
	{
		return strtotime(date('Y-m-d', $this->end()).' - '.$this->period());
	}
	
	public function period($period = NULL)
	{
		if ($period !== NULL)
		{
			$this->_period = $period;
			return $this;
		}

		if ( ! trim($this->_period))
		{
			$this->_period = (round(abs($this->start() - $this->end()) / Date::DAY) + 1).' days';
		}
		return $this->_period;
	}
}