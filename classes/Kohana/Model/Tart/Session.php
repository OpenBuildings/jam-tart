<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * A model for the login form
 *
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
abstract class Kohana_Model_Tart_Session extends Jam_Validated {

	public $login_on_check;

	public static function initialize(Jam_Meta $meta)
	{

		$meta->fields(array(
			'email' => Jam::field('string'),
			'password' => Jam::field('string'),
			'remember_me' => Jam::field('boolean'),
		));

		$meta
			->validator('email', 'password', array('present' => TRUE));
	}

	public function validate()
	{
		if ($this->login_on_check AND $this->is_valid() AND ! $this->login())
		{
			$this->errors()->add('email', 'login');
		}
	}

	public function login()
	{
		return Auth::instance()->login($this->email, $this->password, $this->remember_me);
	}
}