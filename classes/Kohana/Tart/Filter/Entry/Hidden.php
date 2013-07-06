<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Tart_Filter_Entry_Hidden definition
 *
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
abstract class Kohana_Tart_Filter_Entry_Hidden extends Tart_Filter_Entry {

	public function render()
	{
		return $this->parent()->form()->row('hidden', $this->name());
	}

}