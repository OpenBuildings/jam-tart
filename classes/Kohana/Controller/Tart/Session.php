<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Controller_Tart_Session definition
 *
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
abstract class Kohana_Controller_Tart_Session extends Controller_Tart_Layout {

	public $template = 'tart/layout/simple';
	public $access = 'public';

	public function action_new()
	{
		$session = Jam::build('tart_session', $this->request->post());
		$session->login_on_check = TRUE;
		
		if ($this->request->method() === Request::POST AND $session->check())
		{
			$this->redirect(Session::instance()->get_once('requested_url', Tart::uri()));
		}
		else
		{
			$this->template->content = View::factory('tart/session/new', array(
				'session' => $session,
			));
		}
	}


	public function action_destroy()
	{
		Auth::instance()->logout(TRUE, TRUE);
		$this->redirect(Tart::uri('session', 'new'));
	}
}