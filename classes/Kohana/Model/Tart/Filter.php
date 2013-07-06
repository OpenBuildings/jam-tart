<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * A model used specifically for the filters in the admin
 *
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
abstract class Kohana_Model_Tart_Filter extends Jam_Validated {

	public static function initialize(Jam_Meta $meta)
	{
		$meta->fields(array(
			'q' => Jam::field('string'),
		));
	}

}