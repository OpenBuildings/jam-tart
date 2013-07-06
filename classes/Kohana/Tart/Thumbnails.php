<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Tart_Thumbnails definition
 *
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
abstract class Kohana_Tart_Thumbnails extends Tart_Interface_Collection {

	protected $_thumbnail = NULL;
	
	public function thumbnail($thumbnail = NULL)
	{
		if ($thumbnail !== NULL)
		{
			$this->_thumbnail = $thumbnail;
			return $this;
		}
		return $this->_thumbnail;
	}

	public function render()
	{
		$html = Tart::html($this, function($h, $self){
			$h('div.thumbnails', function($h, $self) {
 
				foreach ($self->collection() as $item)
				{
					$h('div.media', function($h, $self) use ($item) {
						if ($self->selected() !== NULL)
						{
							$h('td', function($h, $self) use ($item) {
								$h('input', array('type' => 'checkbox', 'name' => 'id[]', 'value' => Jam_Form::list_id($item), 'checked' => in_array(Jam_Form::list_id($item), $self->selected()) ? TRUE : NULL));
							});
						}
						foreach ($self->columns() as $column)
						{
							$h('td', $column->render($item, $self));
						}
					});
				}

				if ($self->footer())
				{
					$h('tfoot', $self->footer());
				}
			});
		});
		return $html->render();
	}
}