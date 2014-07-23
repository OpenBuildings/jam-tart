<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Tart_Pagination definition
 *
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
abstract class Kohana_Tart_Pagination extends Tart_Interface {

	protected $_total = 0;
	protected $_offset = 0;
	protected $_controller = NULL;
	protected $_per_page = 50;

	public function __construct($total = 0, $offset = 0)
	{
		$this->total($total);
		$this->offset($offset);
	}

	public function apply(Jam_Query_Builder_Collection $collection)
	{
		$collection
			->limit($this->per_page())
			->offset($this->offset());

		return $this;
	}

	public function per_page($per_page = NULL)
	{
		if ($per_page !== NULL)
		{
			$this->_per_page = $per_page;
			return $this;
		}
		return $this->_per_page;
	}

	public function previous()
	{
		return Tart::html($this, function($h, $self) {
			if ($self->offset() > 0)
			{
				$h->anchor(Tart::uri($self->controller()).URL::query(array('offset' => max(0, $self->offset() - $self->per_page()))), __('&laquo; Previous'));
			}
			else
			{
				$h('span', __('&laquo; Previous'));
			}
		});
	}

	public function next()
	{
		return Tart::html($this, function($h, $self) {

			if ($self->offset() < ($self->total() - $self->per_page()))
			{
				$h->anchor(Tart::uri($self->controller()).URL::query(array('offset' => $self->offset() + $self->per_page())), __('Next &raquo;'));
			}
			else
			{
				$h('span', __('Next &raquo;'));
			}
		});
	}

	public function current()
	{
		return $this->offset().' / '.$this->total();
	}

	public function pager()
	{
		return $this->previous().' <span class="divider">'.($this->offset().' / '.$this->total()).'</span> '.$this->next();
	}

	public function render()
	{
		if ($this->total() <= $this->per_page())
			return NULL;

		return Tart::html($this)
			->form(Tart::uri($this->controller()), array('class' => 'form-inline', 'method' => 'GET'), function($h, $self) {

				$h('ul.pager', function($h, $self) {

					$h('li.previous', $self->previous());

					$h('li.next', $self->next());

					$h('li.pagination-control', function($h, $self) {
						$h('label', function($h, $self) {
							$h->add("Showing: ".$self->offset().' - '.min($self->offset() + $self->per_page(), $self->total()).' of '.$self->total());
						});
						$h('span', array('style' => 'display:none'), function($h, $self){
							foreach (Request::initial()->query() as $key => $value)
							{
								$h('input', array('type' => 'hidden', 'name' => $key, 'value' => $value));
							}
							$h('input', array('id' => 'pagination-slider', 'type' => 'range', 'class' => 'input-large', 'min' => 0, 'step' => $self->per_page(), 'value' => $self->offset(), 'max' => $self->total()));
							$h('input', array('id' => 'pagination-input', 'type' => 'number', 'name' => 'offset', 'class' => 'input-mini', 'min' => 0, 'step' => $self->per_page(), 'value' => $self->offset(), 'max' => $self->total()));
							$h('button', array('type' => 'submit', 'class' => 'btn'), __('Go'));
						});
					});

				});
			});
	}

	public function offset($offset = NULL)
	{
		if ($offset !== NULL)
		{
			$this->_offset = $offset;
			return $this;
		}
		return $this->_offset;
	}

	public function total($total = NULL)
	{
		if ($total !== NULL)
		{
			$this->_total = $total;
			return $this;
		}
		return $this->_total;
	}
}