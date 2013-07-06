<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Builder_Xml definition
 *
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
abstract class Kohana_Builder_Html extends Builder_Xml {

	public static $tags = array('a', 'abbr', 'acronym', 'address', 'applet', 'area', 'article', 'aside', 'audio', 'b', 'base', 'basefont', 'bdi', 'bdo', 'big', 'blockquote', 'body', 'br', 'button', 'canvas', 'caption', 'center', 'cite', 'code', 'col', 'colgroup', 'command', 'datalist', 'dd', 'del', 'details', 'dfn', 'dialog', 'dir', 'div', 'dl', 'dt', 'em', 'embed', 'fieldset', 'figcaption', 'figure', 'font', 'footer', 'form', 'frame', 'frameset', 'h1', 'h2', 'h3', 'h4', 'h5', 'head', 'header', 'hgroup', 'hr', 'html', 'i', 'iframe', 'img', 'input', 'ins', 'kbd', 'keygen', 'label', 'legend', 'li', 'link', 'map', 'mark', 'menu', 'meta', 'meter', 'nav', 'noframes', 'noscript', 'object', 'ol', 'optgroup', 'option', 'output', 'p', 'param', 'pre', 'progress', 'q', 'rp', 'rt', 'ruby', 's', 'samp', 'script', 'section', 'select', 'small', 'source', 'span', 'strike', 'strong', 'style', 'sub', 'summary', 'sup', 'table', 'tbody', 'td', 'textarea', 'tfoot', 'th', 'thead', 'time', 'title', 'tr', 'track', 'tt', 'u', 'ul', 'var', 'video', 'wbr');

	public function build($arguments)
	{
		return new Builder_Html($arguments['name'], $arguments['attributes'], $arguments['content'], $this->_context);
	}

	public static function parse_css_name($name)
	{
		if ((strpos($name, '.') !== FALSE OR strpos($name, '#') !== FALSE))
		{
			preg_match('/(?P<name>[a-z][a-z0-9]*)(?P<id>\#[a-zA-Z][a-zA-Z0-9\-_]*)?(?P<classes>(\.[a-zA-Z][a-zA-Z0-9\-_]*)+)?/', $name, $matches);
			
			$attributes = array();

			if ( ! empty($matches['id']))
			{
				$attributes['id'] = substr($matches['id'], 1);
			}
			if ( ! empty($matches['classes']))
			{
				$attributes['class'] = str_replace('.', ' ', substr($matches['classes'], 1));
			}

			return array('tag' => $matches['name'], 'attributes' => $attributes);
		}
		else
		{
			return NULL;
		}
	}

	public function name($name = NULL)
	{
		if ($name !== NULL)
		{
			if ($css_attributes = Builder_Html::parse_css_name($name))
			{
				$this->_name = $css_attributes['tag'];
				$this->attributes($css_attributes['attributes']);
			}
			else
			{
				$this->_name = $name;
			}

			if ( ! in_array($this->_name, Builder_Html::$tags))
				throw new Kohana_Exception('Tag name :name not allowed in HTML 5', array(':name' => $this->_name));

			return $this;
		}
		return $this->_name;
	}

	public function anchor($href)
	{
		$arguments = Builder_Html::arguments(array_merge(array('a'), array_slice(func_get_args(), 1)));
		$arguments['attributes']['href'] = $href;
		return $this->add($arguments);
	}

	public function form($action)
	{
		$arguments = Builder_Html::arguments(array_merge(array('form'), array_slice(func_get_args(), 1)));
		$arguments['attributes'] = array_merge(array(
			'action' => $action,
			'method' => 'POST',
			'enctype' => 'multipart/form-data'
		), (array) $arguments['attributes']);

		return $this->add(new Builder_Form($arguments['name'], $arguments['attributes'], $arguments['content'], $this->_context));
	}
}