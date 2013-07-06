<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Represents a country in the database.
 *
 * @package  Tart
 */
class Model_Test_Region extends Jam_Model {

	public static function initialize(Jam_Meta $meta)
	{
		$meta
			->db(Functest_Fixture_Database::instance()->db_name())

			->behaviors(array(
				'nested' => Jam::behavior('nested'),
			))

			->fields(array(
				'id'   => Jam::field('primary'),
				'name' => Jam::field('string'),
			))

			->validator('name', array('length' => array('minimum' => 4)))
			->validator('name', array('present' => TRUE));
	}
}