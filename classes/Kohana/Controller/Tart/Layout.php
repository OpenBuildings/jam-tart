<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Controller_Tart_Layout definition
 *
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
abstract class Kohana_Controller_Tart_Layout extends Controller_Template {

	public $template = 'tart/layout/template';
	public $access = 'private';

	public function before()
	{
		parent::before();

		$access = Auth_Jam::access($this->request->action(), $this->access);

		if ($access === 'private' AND ( ! Auth::instance()->logged_in() OR ! Tart::user_allowed($this->request->uri(), Auth::instance()->get_user())))
		{
			if ( ! Auth::instance()->logged_in())
			{
				$this->notify('warning', 'You must be logged in to access this page');
			}
			else
			{
				$this->notify('warning', 'Your user does not have access to "'.$this->request->uri().'" page');
			}

			Session::instance()->set('requested_url', $this->request->uri());
			$this->redirect(Tart::uri('session', 'new'));
		}

		$this->template->title = $this->title();
		$this->template->sidebar = FALSE;
	}

	public function title()
	{
		$name = str_replace('Controller_Tart_', '', get_class($this));
		$name = ucwords(Inflector::humanize($name));

		if ($this->request->param('id'))
		{
			return ucwords(Inflector::singular($name)).' - '.Inflector::humanize($this->request->action());
		}
		else
		{
			return $name.' - '.Inflector::humanize($this->request->action());
		}
	}

	public function action_batch()
	{
		$ids = $this->request->post('id') ?: $this->request->query('id');
		$action = $this->request->post('action') ?: $this->request->query('action');

		$this->{'batch_'.$action}($ids);
	}

	public function notify($label, $message)
	{
		$notifications = Session::instance()->get('notifications', array());

		$notifications[] = array('label' => $label, 'message' => $message);
		Session::instance()->set('notifications', $notifications);
	}

	public function post($name = NULL)
	{
		$post = Tart_Request::post($this->request->post(), $_FILES);
		
		if ($name !== NULL)
			return Arr::get($post, $name);

		return $post;
	}
}