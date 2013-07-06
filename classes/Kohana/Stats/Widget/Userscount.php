<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Stats_Widget_Userscount definition
 *
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
abstract class Kohana_Stats_Widget_Userscount extends Stats_Widget_Number {

	public function retrieve($from, $to)
	{
		return Jam::all('user')->where('created_at', 'BETWEEN', array(date('Y-m-d', $from), date('Y-m-d', $to)))->count_all(TRUE);
	}
}