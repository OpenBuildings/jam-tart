<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Represents a country in the database.
 *
 * @package  Tart
 */
class Model_Test_Country extends Jam_Model {

	public static function initialize(Jam_Meta $meta)
	{
		$meta
			->db(Functest_Fixture_Database::instance()->db_name())

			->associations(array(
				'cities' => Jam::association('hasmany'),
			))

			->fields(array(
				'id'   => Jam::field('primary'),
				'file' => Jam::field('string'),
			))

			->validator('name', array('length' => array('minimum' => 4)))
			->validator('name', array('present' => TRUE));

	}

}