<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Tart_Filters definition
 *
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
abstract class Kohana_Tart_Filter extends Tart_Group {

	protected $_data = array();
	protected $_form = NULL;
	
	public function form($form = NULL)
	{
		if ($form !== NULL)
		{
			$this->_form = $form;
			return $this;
		}

		if ( ! $this->_form)
		{
			$this->_form = Jam::form(Jam::build('tart_filter', $this->data()), 'tart_filter');
		}

		return $this->_form;
	}

	public function __construct(array $data, array $items = NULL)
	{
		$this->data($data);
		$this->items($items);
	}

	public function apply($collection)
	{
		if ( ! $this->controller())
		{
			$this->controller(Inflector::plural($collection->meta()->model()));
		}

		foreach ($this->data() as $name => $value) 
		{
			if ($value AND $this->entries($name))
			{
				$this->entries($name)->apply($collection, $value);
			}
		}
		return $this;
	}

	public function anchor($filter, $value, $title, array $attributes = array())
	{
		$attributes['data-provide'] = 'filters';
		$attributes['data-href'] = Tart::uri($this->controller()).URL::query(array($filter => $value, 'offset' => 0));
		$attributes['draggable'] = 'true';
		$attributes['data-dropzone'] = '.tart-filter';
		return new Builder_Html(isset($attributes['href']) ? 'a' : 'span', $attributes, $title);
	}

	public function term_anchors($filter, $items)
	{
		$self = $this;
		return array_map(function($term) use ($filter, $self) {
			return $self->anchor($filter, $term->id, $term->name(), array('class' => 'muted'));
		}, $items->as_array());
	}

	public function render_active()
	{
		$self = $this;
		return Tart::to_sentence(
			array_values(
				array_filter(
					array_map(
						function($entry) use ($self) { 
							if ( ! $entry->active())
								return NULL;

							$data = $self->data();
							unset($data[$entry->name()]);

							return
								HTML::anchor(Tart::uri($self->controller()).URL::query(array_filter($data), FALSE), $entry->active(), array('class' => 'label'));
						}, 
						$this->entries()
					)
				)
			)
		);
	}

	public function render()
	{
		return Tart::html($this)
			->form(Tart::uri($this->controller()), array('method' => 'GET', 'class' => 'tart-filter'), function($h, $self){
				
					$tabindex = 1;

					foreach ($self->items() as $index => $item) 
					{
						$h->add($item->tabindex($tabindex++)->render());
					}

					$h('div.form-actions', function($h, $self) {
						$h('button', array('class' => 'btn', 'tabindex' => count($self->items())), __('Go'));
					});
				
			})
			->render();
	}

	public function entries($name = NULL, $value = NULL)
	{
		return $this->items($name, $value);
	}

	public function data($key = NULL, $value = NULL)
	{
		if ($key === NULL)
			return $this->_data;
	
		if (is_array($key))
		{
			$this->_data = $key;
		}
		else
		{
			if ($value === NULL)
				return Arr::get($this->_data, $key);
	
			$this->_data[$key] = $value;
		}
	
		return $this;
	}
}