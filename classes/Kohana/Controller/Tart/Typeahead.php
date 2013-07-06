<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Controller_Tart_Typeahead definition
 *
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
abstract class Kohana_Controller_Tart_Typeahead extends Controller {

	public function action_index()
	{
		$q = $this->request->query('query');

		$models = explode(',', $this->request->query('model'));
		$response = array();
		foreach ($models as $model) 
		{
			$name_key = $this->request->query('name') ?: Jam::meta($model)->name_key();
			$model_response = Jam::all($model)
				->limit(5)
				->where_open()
					->where($name_key, 'LIKE', "%{$q}%")
					->or_where(':primary_key', '=', $q)
				->where_close();

			$model_response = array_map(function($item) use ($models, $model) {
				return array(
					'name' => (count($models) > 1 ? ucfirst(Inflector::humanize($model)).' - ' : '').'<span class="typeahead-display-val">'.$item->name().'</span> <small class="muted">('.$item->id().')</small>',
					'id' => $item->id(),
					'model' => $model,
				);
			}, $model_response->as_array());

			$response = array_merge($response, $model_response);
		}

		$this->response->body(json_encode($response));
	}

	public function action_name()
	{
		$q = $this->request->query('query');
		$model = $this->request->query('model');
		$names = Jam::all($model)
			->where(':name_key', 'LIKE', "%{$q}%")
			->limit(5);
		$this->response->body(json_encode($names->as_array(NULL, ':name_key')));
	}

	public function action_remoteselect_template()
	{
		$params = $this->request->query();

		if ($missing = array_diff(array('model', 'id', 'name', 'template'), array_keys($params)))
			throw new Kohana_Exception('You must provide :params query parameter', array(':params' => join(', ', $params)));

		$params['item'] = Jam::find($params['model'], $params['id']);

		if ( ! $params['item'])
		{
			$this->response->body('');
		}
		else
		{
			$view = View::factory(Arr::get($params, 'template'), $params);

			$this->response->body($view->render());
		}
	}
}
