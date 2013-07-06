<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Stats_Widget_Number definition precision
 *
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
abstract class Kohana_Stats_Widget_Number extends Stats_Widget {

	public function format($amount)
	{
		return number_format($amount, 0, '.', ',');
	}
	
	public function render()
	{
		$amount = $this->retrieve($this->range()->start(), $this->range()->end());
		$previous_amount = $this->retrieve($this->range()->previous_start(), $this->range()->previous_end());

		$diff = Stats_Widget::percent_diff($amount, $previous_amount);

		return 
			Tart::html($this, function($h, $self) use ($amount, $diff) {

				$h('div', array('class' => 'caption '.(($diff > 0) ? 'success' : 'warning')), function($h, $self) use ($amount, $diff) {
					$h('h4', $self->title());
					$h('strong', $self->format($amount).' ');

					$trend = ($diff == 0) ? 'icon-minus' : (($diff > 0) ? 'icon-chevron-up' : 'icon-chevron-down');
					$h('span.difference', "<i class='{$trend}'></i> ".number_format($diff, 2).' %');
				});
			})->render();
	}
}