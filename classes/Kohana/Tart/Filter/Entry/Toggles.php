<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Tart_Filter_Entry_Toggles definition
 *
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
abstract class Kohana_Tart_Filter_Entry_Toggles extends Tart_Filter_Entry {

	protected $_vertical = FALSE;
	protected $_all = TRUE;
	
	public function all($all = NULL)
	{
		if ($all !== NULL)
		{
			$this->_all = $all;
			return $this;
		}
		return $this->_all;
	}
	
	public function vertical($vertical = NULL)
	{
		if ($vertical !== NULL)
		{
			$this->_vertical = $vertical;
			return $this;
		}
		return $this->_vertical;
	}

	public function render()
	{
		return Tart::form($this, function($h, $self){
			$h('label', __($self->label()));
			$h('p', function($h, $self) {
				$h('div', array('class' => 'btn-group'.($self->vertical() ? ' btn-group-vertical' : ''), 'data-toggle' => 'buttons-radio'), function($h, $self) {
					if ($self->all())
					{
						$h('a', 
							array(
								'class' => 'btn '.($self->parent()->form()->object()->{$self->name()} ? '' : ' active'), 
								'href' => Request::current()->url().URL::query(array($self->name() => ''))
							), 
							__('All')
						);
					}
					foreach ($self->params() as $value => $label) 
					{
						$h('a', 
							array(
								'class' => 'btn '.($self->parent()->form()->object()->{$self->name()} == $value ? ' active' : ''), 
								'href' => Request::current()->url().URL::query(array($self->name() => $value))
							), 
							$label
						);
					}
				});
			});
		});
	}

}