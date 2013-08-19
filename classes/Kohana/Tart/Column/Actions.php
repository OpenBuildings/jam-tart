<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Tart_Column_Actions definition
 *
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
abstract class Kohana_Tart_Column_Actions extends Tart_Column {

	protected $_sortable = FALSE;
	
	/**
	 * Getter / Setter
	 * @param  boolean $sortable 
	 * @return boolean|$this
	 */
	public function sortable($sortable = NULL)
	{
		if ($sortable !== NULL)
		{
			$this->_sortable = $sortable;
			return $this;
		}
		return $this->_sortable;
	}

	/**
	 * Getter / Setter for the width, defaults to 105, and 180 when sortable
	 * @param  integer $width 
	 * @return integer|$this        
	 */
	public function width($width = NULL)
	{
		if ($width !== NULL)
		{
			return parent::width($width);
		}

		if ($this->_width === NULL)
		{
			$this->_width = ($this->sortable() ? 180 : 105);
		}

		return $this->_width;
	}

	/**
	 * Return the controls for sortable (up and down buttons)
	 * @param  mixed $item 
	 * @return string       
	 */
	public function sortable_controls($item)
	{
		return Tart::html($item, function($h, $item) {
			$h('div.btn-group', function($h, $item) {
				$h('span', array(
					'class' => 'btn btn-small js-sortable-handle',
					'data-sortable-id' => $item->id()
				), function($h) use ($item){
					$h('i', array(
						'class' => 'icon-move'
					), ' ');
				});
			});
		});
	}

	public function default_callback()
	{
		$self = $this;
		return function($item) use ($self) {
			return Tart::html($item, function($h, $item) use ($self) {
				if ($self->sortable())
				{
					$h->add($self->sortable_controls($item));
				}
				$params = array();
				if ($self->controller())
				{
					$params['controller'] = $self->controller();
				}
				
				$h->add(Tart_Html::anchor(Tart::uri($item, Arr::merge($params, array('action' => 'edit'))), __('Edit'), array('class' => 'btn btn-small')));
				$h->add(Tart_Html::anchor(Tart::uri($item, Arr::merge($params, array('action' => 'delete'))), __('Delete'), array('class' => 'btn btn-small btn-danger', 'data-confirm' => __('Are you sure you want to delete this :item?', array(':item' => $item->meta()->model())))));
			});
		};
	}
}