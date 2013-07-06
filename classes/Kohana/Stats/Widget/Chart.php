<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Stats_Widget_Chart definition
 *
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
abstract class Kohana_Stats_Widget_Chart extends Stats_Widget {

	public function render()
	{
		$amounts = $this->retrieve($this->range()->start(), $this->range()->end());

		return Tart::html($this, function($h, $self) use ($amounts) {

			$h('div', array('class' => 'caption'), function($h, $self) use ($amounts) {
				$h('h4', $self->title());

				$total = array_sum($amounts);

				$h('p', function($h) use ($self, $amounts, $total) {
					$classes = array(0 => 'label-success', 1 => 'label-info', 2 => 'label-warning', 3 => 'label-danger');
					
					foreach (array_keys($amounts) as $i => $title) 
					{
						$h('div', function($h) use ($self, $amounts, $i, $title, $total, $classes) {
							$h('span', array('class' => 'label '.$classes[$i]), number_format($self::to_percent($amounts[$title], $total), 2).'%');
							$h->add(ucfirst($title));
						});
					}
				});

				$classes = array(0 => 'bar-success', 1 => 'bar-info', 2 => 'bar-warning', 3 => 'bar-danger');

				$h('div.progress', function($h) use ($self, $amounts, $total, $classes) {
					foreach (array_values($amounts) as $i => $amount) 
					{
						$h('div', array('class' => 'bar '.$classes[$i], 'style' => 'width:'.($self::to_percent($amount, $total).'%')));
					}
				});
			});
		})->render();
	}

}