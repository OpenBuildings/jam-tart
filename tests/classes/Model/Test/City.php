<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Represents a city in the database.
 *
 * @package  Tart
 */
class Model_Test_City extends Jam_Model {

	public static function initialize(Jam_Meta $meta)
	{
		$meta
			->db(Kohana::TESTING)

			->associations(array(
				'country' => Jam::association('belongsto'),
			))

			->fields(array(
				'id'          => Jam::field('primary'),
				'name'        => Jam::field('string'),
				'population'  => Jam::field('integer'),
				'lat'         => Jam::field('float'),
				'lon'         => Jam::field('float'),
				'is_big'      => Jam::field('boolean'),
				'has_service' => Jam::field('boolean'),
				'data'        => Jam::field('serialized'),
				'description' => Jam::field('text'),
				'cover'       => Jam::field('upload', array('server' => 'local')),
				'url'         => Jam::field('weblink'),
				'created_at'  => Jam::field('timestamp')

			))

			->validator('name', array('length' => array('minimum' => 4)))
			->validator('name', 'city', array('present' => TRUE));

	}

}