<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Builder_Xml definition
 *
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
abstract class Kohana_Builder_Xml {

	const INDENT = '  ';

	public static $pretty = TRUE;

	public static function arguments($raw)
	{
		$arguments = array('name' => array_shift($raw), 'attributes' => NULL, 'content' => NULL);

		foreach ($raw as $argument) 
		{
			if (is_array($argument))
			{
				$arguments['attributes'] = $argument;
			}
			else
			{
				$arguments['content'] = $argument;
			}
		}
		return $arguments;
	}

	public static function render_tag($item, $indent = 0)
	{
		return ($item instanceof Builder_Xml) ? $item->render($indent) : Builder_Xml::indent_string($indent).$item;
	}

	public static function indent_string($indent = 0)
	{
		return (int) $indent > 0 ? str_repeat(Builder_Xml::INDENT, (int) $indent) : '';;
	}

	protected $_context;
	protected $_children;
	protected $_content;
	protected $_attributes;
	protected $_name;
	protected $_preserve = FALSE;
	
	public function preserve($preserve = NULL)
	{
		if ($preserve !== NULL)
		{
			$this->_preserve = (int) $preserve;
			return $this;
		}
		return $this->_preserve;
	}

	public function __construct($name = NULL, array $attributes = NULL, $content = NULL, $context = NULL)
	{
		$this->_context = $context;

		$this->name($name);
		$this->attributes($attributes);
		$this->content($content);
		
	}

	public function __invoke($tag)
	{
		$arguments = Builder_Xml::arguments(func_get_args());
		$this->add($arguments);
	}

	public function tag($tag)
	{
		$arguments = Builder_Xml::arguments(func_get_args());
		return $this->add($arguments);
	}

	public function add($content)
	{
		$this->_children []= is_array($content) ? $this->build($content) : $content;
		return $this;
	}

	public function build($arguments)
	{
		return new Builder_Xml($arguments['name'], $arguments['attributes'], $arguments['content'], $this->_context);
	}

	public function render($indent = 0)
	{
		if ( ! $this->_name)
			return $this->render_content();

		$attributes = HTML::attributes($this->_attributes);

		$indent_string = Builder_Xml::indent_string($indent);

		if ($this->has_content())
		{
			$content = $this->render_content($this->preserve() ? -1 : $indent + 1);

			if ( ! $this->preserve())
			{
				$content = strlen($content) > 30 ? "\n{$content}\n{$indent_string}" : trim($content);
			}

			return $indent_string."<{$this->_name}{$attributes}>{$content}</{$this->_name}>";
		}
		else
		{
			return $indent_string."<{$this->_name}{$attributes}/>";
		}
	}

	public function render_content($indent = 0)
	{
		$content = $this->_content;

		if ($content instanceof Closure)
		{
			$content = $content($this, $this->_context);
		}

		if ($content)
		{
			$content = Builder_Xml::render_tag($content, $indent);	
		}
		
		if ($this->_children)
		{
			$content .= join(Builder_Xml::$pretty ? "\n" : '', array_map(function($content) use ($indent) { return Builder_Xml::render_tag($content, $indent); }, (array) $this->_children));
		}

		return $content;
	}

	public function has_content()
	{
		return ($this->_content OR $this->_children);
	}

	public function __toString()
	{
		return $this->render();
	}

	public function children()
	{
		return $this->_children;
	}

	public function name($name = NULL)
	{
		if ($name !== NULL)
		{
			$this->_name = $name;
			return $this;
		}
		return $this->_name;
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

	public function content($content = NULL)
	{
		if ($content !== NULL)
		{
			$this->_content = $content;
			return $this;
		}
		return $this->_content;
	}
}