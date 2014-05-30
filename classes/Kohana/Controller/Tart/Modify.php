<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Controller_Tart_Modify definition
 *
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
abstract class Kohana_Controller_Tart_Modify extends Controller{

	public function action_up()
	{
		$item = Jam::find_insist($this->request->query('model'), $this->request->param('id'));
		$item->increase_position();
		$this->redirect($this->request->referrer());
	}

	public function action_down()
	{
		$item = Jam::find_insist($this->request->query('model'), $this->request->param('id'));
		$item->decrease_position();
		$this->redirect($this->request->referrer());
	}


	public function action_move()
	{
		$item = Jam::find_insist($this->request->query('model'), $this->request->query('from'));
		$second_item = Jam::find_insist($this->request->query('model'), $this->request->query('to'));

		$item->move_position_to($second_item);

		$this->response->body('OK');
	}

	public function action_reorder()
	{
		if ($this->request->method() === Request::POST)
		{
			Jam::all($this->request->query('model'))
				->sort_ids(Arr::get($this->request->post(), 'item', array()));

			$this->response->body('OK');
		}
		else
		{
			$this->response->body('Empty post');
		}
	}
}