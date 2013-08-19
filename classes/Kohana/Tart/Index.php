<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Tart_Index definition
 *
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
abstract class Kohana_Tart_Index extends Tart_Interface {

	const BATCH_POSITION_TOP = 'top';
	const BATCH_POSITION_BOTTOM = 'bottom';
	const BATCH_POSITION_BOTH = 'both';

	protected $_pagination;
	protected $_offset;
	protected $_batch_actions = array();
	protected $_content;
	protected $_collection;
	protected $_batch_position = Kohana_Tart_Index::BATCH_POSITION_TOP;

	public function collection(Jam_Query_Builder_Collection $collection = NULL)
	{
		if ($collection !== NULL)
		{
			$this->_collection = $collection;
			return $this;
		}
		return $this->_collection;
	}
	
	function __construct(Jam_Query_Builder_Collection $collection, $offset, array $columns = array()) 
	{
		$this->collection($collection);
		$this->controller(Inflector::plural($collection->meta()->model()));
		$this->columns($columns);

		$this->_offset = $offset;
	}
	
	public function batch_position($batch_position = NULL)
	{
		if ($batch_position !== NULL)
		{
			$this->_batch_position = $batch_position;
			return $this;
		}

		return $this->_batch_position;
	}

	public function content(Tart_Interface_Collection $content = NULL)
	{
		if ($content !== NULL)
		{
			$this->_content = $content;
			$this->_content->parent($this);
			return $this;
		}
		return $this->_content;
	}

	public function pagination($pagination = NULL)
	{
		if ($pagination !== NULL)
		{
			$this->_pagination = $pagination;
			$this->_pagination->parent($this);
			return $this;
		}

		if ($this->_pagination === NULL)
		{
			$pagination = Tart::pagination($this->collection()->count_all(TRUE), $this->_offset);
			$this->pagination($pagination);
		}

		return $this->_pagination;
	}
	
	public function batch_actions($key = NULL, $value = NULL)
	{
		if ($key === NULL)
			return $this->_batch_actions;
	
		if (is_array($key))
		{
			$this->_batch_actions = $key;
		}
		else
		{
			if ($value === NULL)
				return Arr::get($this->_batch_actions, $key);
	
			$this->_batch_actions[$key] = $value;
		}
	
		return $this;
	}

	public function columns($name = NULL, $value = NULL)
	{
		if ( ! $this->_content)
		{
			$this->content(Tart::table());
		}

		if (is_array($name) OR $value !== NULL)
		{
			$this->content()->columns($name, $value);
			return $this;
		}
		else
		{
			return $this->content()->columns($name, $value);
		}
	}

	public function render_batch_actions()
	{
		if ( ! $this->batch_actions())
			return NULL;
		
		return Tart::html($this, function($h, $self){
			$h('div.form-inline', function($h, $self) {
				$h('label', 'With Selected: ');
				foreach ($self->batch_actions() as $action => $title) 
				{
					$h('button', array('type' => 'submit', 'name' => 'action', 'value' => $action, 'class' => 'btn'), $title);	
				}
			});
		});
	}

	public function render()
	{
		$this->pagination()->apply($this->collection());

		$content = $this->content()->collection($this->collection());

		if ($this->batch_actions())
		{
			$content->selected(array());
		}
		else
		{
			$content->selected(FALSE);	
		}

		$content = $content->render();

		$html = Tart::html($this, function($h, $self) use ($content) {
			$h->form(Tart::uri($self->controller(), 'batch'), array('method' => 'get'), function($h, $self) use ($content) {
				if ($self->batch_position() == Kohana_Tart_Index::BATCH_POSITION_BOTH OR $self->batch_position() == Kohana_Tart_Index::BATCH_POSITION_TOP)
				{
					$h->add($self->render_batch_actions());
				}

				$h->add($content);

				if ($self->batch_position() == Kohana_Tart_Index::BATCH_POSITION_BOTH OR $self->batch_position() == Kohana_Tart_Index::BATCH_POSITION_BOTTOM)
				{
					$h->add($self->render_batch_actions());
				}
			});

			$h->add($self->pagination()->render());
		});

		return $html->render();
	}
}