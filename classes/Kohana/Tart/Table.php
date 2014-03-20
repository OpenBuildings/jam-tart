<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Tart_Table definition
 *
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
abstract class Kohana_Tart_Table extends Tart_Group {

	protected $_footer;
	protected $_attributes = array('class' => 'table table-striped table-hover');

	function __construct($collection = array(), array $items = array())
	{
		$this->collection($collection);
		$this->items($items);
	}

	public static function item_class_name($item)
	{
		return ($item instanceof Jam_Validated ? $item->meta()->model().'-'.$item->id() : NULL);
	}

	public function attributes($key = NULL, $value = NULL)
	{
		if ($key === NULL)
			return $this->_attributes;

		if (is_array($key))
		{
			$this->_attributes = $key;
		}
		else
		{
			if ($value === NULL)
				return Arr::get($this->_attributes, $key);

			$this->_attributes[$key] = $value;
		}

		return $this;
	}

	public function footer($footer = NULL)
	{
		if ($footer !== NULL)
		{
			$this->_footer = $footer;
			return $this;
		}
		return $this->_footer;
	}

	public function render()
	{
		$html = Tart::html($this, function($h, $self){
			$h('table', $self->attributes(), function($h, $self) {

				$h('thead', function($h, $self) {
					if ($self->selected() !== NULL AND $self->selected() !== FALSE)
					{
						$h('th', array('width' => 10), function($h, $self) {
							$h('input', array('type' => 'checkbox', 'name' => 'all', 'value' => '1', 'checked' => array_diff($self->collection()->ids(), $self->selected()) ? NULL : 'checked'));
						});
					}
					foreach ($self->columns() as $column)
					{
						$h('th', array('width' => $column->width(), 'nowrap'), $column->sort() ? $column->sort_anchor() : $column->label());
					}
				});

				$h('tbody', function($h, $self){
					foreach ($self->collection() as $item)
					{
						$h('tr', array('class' => Tart_Table::item_class_name($item)), function($h, $self) use ($item) {
							if ($self->selected() !== NULL AND $self->selected() !== FALSE)
							{
								$h('td', function($h, $self) use ($item) {
									$h('input', array('type' => 'checkbox', 'name' => 'id[]', 'value' => Jam_Form::list_id($item), 'checked' => in_array(Jam_Form::list_id($item), $self->selected()) ? TRUE : NULL));
								});
							}
							foreach ($self->columns() as $column)
							{
								$h('td', $column->item($item)->render());
							}
						});
					}
				});

				if ($self->footer())
				{
					$h('tfoot', $self->footer());
				}
			});
		});

		return $html->render();
	}

	public function to_csv()
	{
		if ( ! ($handle = fopen('php://output', 'w+')))
			throw new Kohana_Exception('Cannot open php://output for writing');

		ob_start();
		fputcsv($handle, array_map(function($column){ return $column->label(); }, $this->columns()));

		foreach ($this->collection() as $item)
		{
			$values = array();

			foreach ($this->columns() as $column)
			{
				$values[] = Text::to_plain($column->item($item)->render());
			}

			fputcsv($handle, $values);
		}

		$csv = ob_get_contents();
		ob_clean();
		fclose($handle);

		return $csv;
	}

	public function columns($name = NULL, $value = NULL)
	{
		return $this->items($name, $value);
	}

	public function __toString()
	{
		return $this->render();
	}
}