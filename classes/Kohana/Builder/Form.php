<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Builder_Xml definition
 *
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
abstract class Kohana_Builder_Form extends Builder_Html {

	public function build($arguments)
	{
		return new Builder_Form($arguments['name'], $arguments['attributes'], $arguments['content'], $this->_context);
	}

	public function input($name, $value = NULL, array $attributes = NULL)
	{
		$attributes = array_merge(array(
			'name' => $name,
			'type' => 'text',
			'value' => $value,
		), (array) $attributes);

		return $this->add(new Builder_Html('input', $attributes, NULL, $this->_context));
	}

	public function hidden($name, $value = NULL, array $attributes = NULL)
	{
		$attributes['type'] = 'hidden';

		return $this->input($name, $value, $attributes);
	}	

	public function password($name, array $attributes = NULL)
	{
		$attributes['type'] = 'password';

		return $this->input($name, NULL , $attributes);
	}	

	public function file($name, array $attributes = NULL)
	{
		$attributes['type'] = 'file';

		return $this->input($name, NULL, $attributes);
	}

	public function checkbox($name, $value = NULL, $checked = FALSE, array $attributes = NULL)
	{
		$attributes['type'] = 'checkbox';

		if ($checked === TRUE)
		{
			$attributes[] = 'checked';
		}

		return $this->input($name, $value, $attributes);
	}

	public function radio($name, $value = NULL, $checked = FALSE, array $attributes = NULL)
	{
		$attributes['type'] = 'radio';

		if ($checked === TRUE)
		{
			$attributes[] = 'checked';
		}

		return $this->input($name, $value, $attributes);
	}

	public function textarea($name, $value = NULL, array $attributes = NULL)
	{
		$attributes = array_merge(array(
			'name' => $name,
			'rows' => 10,
			'cols' => 50,
		), (array) $attributes);

		return $this->add(new Builder_Html('textarea', $attributes, HTML::chars($value), $this->_context));
	}

	public function select($name, array $choices = NULL, $selected = NULL, array $attributes = array())
	{
		$attributes = array_merge(array(
			'name' => $name,
		), (array) $attributes);

		$select = new Builder_Form('select', $attributes, NULL, $this->_context);

		if ( ! is_array($selected))
		{
			if ($selected === NULL)
			{
				// Use an empty array
				$selected = array();
			}
			else
			{
				// Convert the selected options to an array
				$selected = array( (string) $selected);
			}
		}

		if ($choices)
		{
			foreach ($choices as $key => $value) 
			{
				if (is_array($value))
				{
					$select('optgroup', function($optgroup) use ($value, $selected) {
						foreach ($value as $key => $child_value) 
						{
							$optgroup('option', array('value' => $key, in_array($key, $selected) ? 'selected' : NULL), $child_value);
						}
					});
				}
				else
				{
					$select('option', array('value' => $key, in_array($key, $selected) ? 'selected' : NULL), $value);
				}
			}
		}

		return $this->add($select);
	}

	public function label($input)
	{
		$arguments = Builder_Html::arguments(array_merge(array('label'), array_slice(func_get_args(), 1)));
		$arguments['attributes']['for'] = $input;
		return $this->add($arguments);
	}
}