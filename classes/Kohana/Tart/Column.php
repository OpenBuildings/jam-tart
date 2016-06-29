<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Tart_Column definition
 *
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
abstract class Kohana_Tart_Column extends Tart_Group_Item {

	protected $_filter = NULL;
	protected $_width = NULL;
	protected $_is_link = NULL;
	protected $_filter_name = NULL;
	protected $_item;
	protected $_sort = TRUE;


	function __construct($callback = NULL)
	{
		$this->callback($callback);
	}

	public function item($item = NULL)
	{
		if ($item !== NULL)
		{
			$this->_item = $item;
			return $this;
		}
		return $this->_item;
	}

	public function width($width = NULL)
	{
		if ($width !== NULL)
		{
			$this->_width = $width;
			return $this;
		}
		return $this->_width;
	}

	public function sort($sort = NULL)
	{
		if ($sort !== NULL)
		{
			$this->_sort = $sort;
			return $this;
		}

		return $this->_sort;
	}

	public function set_link($link)
	{
		$this->_is_link = $link;
		return $this;
	}

	public function set_filter($filter, $name)
	{
		$this->_filter = $filter;
		$this->_filter_name = $name;

		return $this;
	}

	public function filter()
	{
		return $this->_filter;
	}

	public function filter_name()
	{
		return $this->_filter_name;
	}

	public function render()
	{
		if ( ! $this->item())
			throw new Kohana_Exception('You must assign an item before you render');

		if ($callback = $this->callback())
		{
			$content = call_user_func($callback, $this->item(), $this->name(), $this);
		}
		elseif ($field = $this->item()->meta()->field($this->name()))
		{
			$content = Tart_Column::render_field($this->item(), $field);
		}
		elseif ($association = $this->item()->meta()->association($this->name()))
		{
			$content = Tart_Column::render_association($this->item(), $association);
		}
		else
		{
			$content = (string) $this->item()->{$this->name()};
		}

		if ($content)
		{
			if ($this->_filter AND $this->_filter_name AND $this->_is_link)
			{
				$content = $this->_filter->anchor($this->_filter_name, Jam_Form::list_id($this->item()->{$this->name()}), $content, array('href' => Tart::uri($this->item()->{$this->name()})));
			}

			if ($this->_is_link)
			{
				$content = HTML::anchor(Tart::uri($this->item()->{$this->name()}), $content);
			}
			if ($this->_filter AND $this->_filter_name)
			{
				$content = $this->_filter->anchor($this->_filter_name, Jam_Form::list_id($this->item()->{$this->name()}), $content);
			}
		}

		return $content;
	}

	public function sort_anchor()
	{
		$direction = 'asc';

		if ($sort = Arr::get(Request::initial()->query(), 'sort'))
		{
			list($column, $current_direction) = explode(':', $sort);
		}

		if (isset($column) AND $column == $this->name())
		{
			$direction = ($current_direction == 'asc') ? 'desc' : 'asc';
		}
		else
		{
			$current_direction = NULL;
		}

		$class = ($current_direction) ? 'icon icon-chevron-'.(($current_direction == 'asc') ? 'up' : 'down') : '';

		return
			HTML::anchor(Tart::uri($this->controller()).URL::query(array('sort' => $this->name().':'.$direction)),
			$this->label().' '.'<i class="'.$class.'"></i>');
	}

	protected static function render_association(Jam_Model $item, Jam_Association $association)
	{
		$value = $item->{$association->name};

		if ($association instanceof Jam_Association_Collection)
		{
			return count($value);
		}
		elseif ($value instanceof Jam_Model)
		{
			$model = $value->meta()->model();
			return $value->name();
		}
	}

	protected static function render_field(Jam_Model $item, Jam_Field $field)
	{
		$value = $item->{$field->name};

		if ($field instanceof Jam_Field_Integer)
		{
			return HTML::chars(number_format($value));
		}
		elseif ($field instanceof Jam_Field_Float)
		{
			return HTML::chars(number_format($value, 2));
		}
		elseif ($field instanceof Jam_Field_Boolean)
		{
			return $value ? '<i class="icon-ok"></i>' : '';
		}
		elseif ($field instanceof Jam_Field_Serialized)
		{
			return Debug::vars($value);
		}
		elseif ($field instanceof Jam_Field_Timestamp)
		{
			if ( ! $value)
				return '-';

			$time = is_numeric($value) ? $value : strtotime($value);
			return '<span title="'.date('j M Y H:i:s', $time).'">'.Tart_Html::date_span($time).'</span>';
		}
		elseif ($field instanceof Jam_Field_Weblink)
		{
			return Text::limit_chars(HTML::chars($value), 30).'&nbsp;'.HTML::anchor($value, '<i class="icon-share-alt"></i>');
		}
		elseif ($field instanceof Jam_Field_Text)
		{
			return Text::widont(Text::limit_chars(HTML::chars($value), 40));
		}
		elseif ($field instanceof Jam_Field_Upload)
		{
			return HTML::image($value->url(TRUE), array('class' => 'img-polaroid', 'alt' => $item->name()));
		}
		else
		{
			return HTML::chars($value);
		}
	}
}
