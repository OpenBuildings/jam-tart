<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Tart_Filter_Entry_Remoteselect definition
 *
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
abstract class Kohana_Tart_Filter_Entry_Remoteselect extends Tart_Filter_Entry {

	public function render()
	{
		$options = Arr::merge(array('label' => __($this->label())), $this->params());

		return $this->parent()->form()->row('remoteselect', $this->name(), $options, array('tabindex' => $this->tabindex()));
	}
}