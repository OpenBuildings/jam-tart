<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Add special fields for the role in the auth module
 *
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
abstract class Kohana_Model_Role extends Model_Auth_Role {

	public static function initialize(Jam_Meta $meta)
	{
		parent::initialize($meta);

		$meta
			->fields(array(
				'allowed' => Jam::field('serialized', array('method' => 'json', 'convert_empty' => TRUE, 'default' => array())),
				'disallowed' => Jam::field('serialized', array('method' => 'json', 'convert_empty' => TRUE, 'default' => array())),
			));
	}
}