<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * All the widgets needed for the admin. 
 * If you feel the need for extra widgets, you can extend this class in your module
 *
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
abstract class Kohana_Jam_Form_Tart_General extends Jam_Form_General {

	/**
	 * The widgets that need to be not indented. E.g. the contents of the textarea
	 * @var array
	 */
	public static $preserve_widgets = array('textarea');

	/**
	 * Get all terms from a vocabulary. You can pass an array of vocabularies.
	 * @param  string|array $vocabulary the vocabulary names
	 * @param  boolean $visible    set this to TRUE or FALSE to show only visible / not visible terms
	 * @return array             a key-value list
	 */
	public static function list_vocabulary_choices($vocabulary, $visible = NULL)
	{
		$choices = Jam::find('vocabulary', $vocabulary)->terms->order_by('parent_id', 'ASC');

		if ($visible !== NULL)
		{
			$choices = $choices->visible($visible);
		}
		$choices_list = array();
		foreach ($choices as $choice) 
		{
			$choices_list[$choice->id] = $choice->parent ? ($choice->parent->name(). ' / '.$choice->name()) : $choice->name();
		}
		return $choices_list;
	}

	/**
	 * Generate a row. Extends the default generator
	 * @param  string $type       the name of the widget
	 * @param  string $name       the name of the field
	 * @param  array  $options    array can include 'label', 'help' and 'clear' all other options are passed to the widget 
	 * @param  array  $attributes array 
	 * @param  string $template   override the template
	 * @return string             
	 */
	public function row($type, $name, array $options = array(), array $attributes = array(), $template = NULL)
	{
		$errors = $this->errors($name);

		return Tart::html($this, function($h, $self) use ($type, $name, $options, $attributes, $errors){
			$classes = array('control-group', 'control-group-'.$type);
			if ($errors)
			{
				$classes[] = 'error';
			}

			$h('div', array('class' => join(' ', $classes)), function($h, $self) use ($type, $name, $options, $attributes, $errors)  {

				$h->add($self->label($name, Arr::get($options, 'label'), array('class' => 'control-label')));

				$h('div.controls', function($h, $self) use ($type, $name, $options, $attributes, $errors) {

					$h->add(Tart::html($self, function($h, $self) use ($type, $name, $options, $attributes, $errors) {
						if (in_array($type, Kohana_Jam_Form_Tart_General::$preserve_widgets));
						{
							$h->preserve(TRUE);
						}
						$h->add(call_user_func(array($self, $type), $name, $options, $attributes));
					}));
					
					$h->add($errors);
					if (isset($options['help']))
					{
						$h('span.help-block', $options['help']);
					}

					if (isset($options['clear']))
					{
						$h('label.checkbox', function($h, $self) use ($name, $options, $attributes) {
							$h('input', array('type' => 'checkbox', 'name' => Arr::get($attributes, 'name', $self->default_name($name)), 'value' => '__clear'));
							$h->add(is_string($options['clear']) ? $options['clear'] : 'Clear');
						});
					}
				});
			});
		})->render();
	}

	/**
	 * The same as row, but places the input and label inline
	 * @param  string $type       
	 * @param  string $name       
	 * @param  array  $options    
	 * @param  array  $attributes 
	 * @param  string $template   
	 * @return string             
	 */
	public function row_inline($type, $name, array $options = array(), array $attributes = array(), $template = NULL)
	{
		$errors = $this->errors($name);

		return Tart::html($this, function($h, $self) use ($type, $name, $options, $attributes, $errors){
			$classes = array('inner-group', 'inner-group-'.$type);
			if ($errors)
			{
				$classes[] = 'error';
			}

			$h('div', array('class' => join(' ', $classes)), function($h, $self) use ($type, $name, $options, $attributes, $errors)  {

				$h->add($self->label($name, Arr::get($options, 'label')));

				$h->add(call_user_func(array($self, $type), $name, $options, $attributes));
				$h->add($errors);
				if (isset($options['help']))
				{
					$h('span.help-block', $options['help']);
				}
			});
		})->render();
	}


	/**
	 * Return the html for the errors for a given field
	 * @param string $name 
	 * @return string 
	 */
	public function errors($name)
	{
		$errors = join(', ', Arr::flatten( (array) $this->object()->errors($name)));
		return $errors ? "<span style=\"display:inline-block;\" class=\"text-error\">{$errors}</span>" : '';
	}

	/**
	 * Get a chozen select for vocabulary.
	 * 
	 * Options
	 *  - vocabulary: string|array, required
	 *  
	 * @param  string $name       
	 * @param  array  $options    
	 * @param  array  $attributes 
	 * @return string             
	 */
	public function taxonomy($name, array $options = array(), array $attributes = array())
	{
		if ( ! isset($options['vocabulary']))
			throw new Kohana_Exception('You must set an option "vocabulary" (array or string)');

		if (is_array($options['vocabulary']))
		{
			$choices = array();
			foreach ($options['vocabulary'] as $vocabulary) 
			{
				$choices[$vocabulary] = Jam_Form_Tart_General::list_vocabulary_choices($vocabulary, Arr::get($options, 'visible', TRUE));
			}	
		}
		else
		{
			$choices = Jam_Form_Tart_General::list_vocabulary_choices($options['vocabulary'], Arr::get($options, 'visible', TRUE));
		}
		$options['choices'] = $choices;
		$attributes = Jam_Form::add_class($attributes, 'chzn-select');
		$attributes['multiple'] = Arr::get($attributes, 'multiple', TRUE);
		$attributes['name'] = $this->default_name($name).'[]';
		return $this->select($name, $options, $attributes);
	}

	/**
	 * A widget to upload an image
	 * 
	 * Options:
	 * 	- remove: boolean, add a "remove" button, defaults true
	 * 	- thumbnail: string, the name of the thumbnail to display, if not set uses the full image
	 * 	
	 * @param  string $name       
	 * @param  array  $options    
	 * @param  array  $attributes 
	 * @return string             
	 */
	public function upload($name, array $options = array(), array $attributes = array())
	{
		$remove = Arr::get($options, 'remove', TRUE);

		if ( ! ($this->object()->{$name} instanceof Upload_File))
			throw new Kohana_Exception('Upload widget is used for upload file fields only for name :name', array(':name' => $name));;

		return Tart::html($this, function($h, $self) use ($name, $options){
			$h('div', array('class' => 'fileupload '.(($self->object()->{$name}->is_empty()) ? 'fileupload-new' : 'fileupload-exists'), 'data-provides' => 'fileupload'), function($h, $self) use ($name, $options) {
				$h('div', array('class' => 'fileupload-new thumbnail', 'style' => 'width:100px; height:75px;line-height:75px;text-align:center;'), function($h) {
					$h('img', array('src' => 'http://www.placehold.it/100x75/EFEFEF/AAAAAA&text=no+image'));
				});

				$h('div', array('class' => 'fileupload-preview fileupload-exists thumbnail', 'style' => 'width:100px; height:75px;line-height:75px;text-align:center;'), function($h, $self) use ($name, $options) {
					if ( ! $self->object()->$name->is_empty())
					{
						$thumbnail = Arr::get($options, 'thumbnail');

						$h('img', array('src' => $self->object()->$name->url($thumbnail, TRUE)));
					}
				});

				$h('span.btn.btn-file', function($h, $self) use ($name){
					$h('span.fileupload-new', 'Select Image');
					$h('span.fileupload-exists', 'Change Image');
					$h->add($self->file($name, array('temp_source' => TRUE)));
				});

				$remove = Arr::get($options, 'remove');

				if ($remove)
				{
					$h('a', array('href' => '#', 'class' => 'btn fileupload-exists', 'data-dismiss' => 'fileupload'), 'Remove');
				}
			});
		})->render();
	}

	/**
	 * A widget used to input an array of string values, usually stored on a serialized field
	 * 
	 * @param  string $name       
	 * @param  array  $options    
	 * @param  array  $attributes 
	 * @return string             
	 */
	public function input_array($name, array $options = array(), array $attributes = array())
	{
		$attributes = $this->default_attributes($name, $attributes);

		return Tart::html($this, function($h) use ($name, $attributes) {
			$h('div', array('id' => $attributes['id'], 'data-index' => $attributes['name'].'[{{index}}]'), function($h, $self) use ($name, $attributes) {
				$h('input', array('type' => 'hidden', 'name' => $attributes['name'].'[]', 'value' => ''));
				foreach ( (array) $self->object()->$name as $index => $value) 
				{
					$h('div.multiform', function($h, $self) use ($attributes, $index, $value) {
						$h('input', array('type' => 'text', 'name' => $attributes['name']."[{$index}]", 'value' => $value, 'class' => Arr::get($attributes, 'class')));
						$h('button', array('class' => 'btn', 'data-dismiss' => 'multiform'), __('Remove'));
					});
				}
				$h('a', array('href' => "#{$name}-form", 'class' => 'btn', 'data-multiform-add' => '#'.$attributes['id']), __('Add'));
				$h('fieldset', array('disabled', 'class' => 'hide', 'id' => "{$name}-form"), function($h) use ($attributes) {
					$h('input', array('type' => 'text', 'name' => $attributes['name'].'[0]', 'class' => Arr::get($attributes, 'class')));
					$h('button', array('class' => 'btn', 'data-dismiss' => 'multiform'), 'Remove');
				});
			});
		});
	}
	
	/**
	 * A widget to enter a string value for a field, using typeahead. 
	 * If its a model, uses :name_key for that model to dispaly the value
	 *
	 * Options:
	 * 	- model: string, defaults to the foreign_model of the associaton, can be comma separated
	 * 	- source: string, the url used to retrieve the typeahead data. Defaults to the builtin typeahead action
	 * @param  string $name       
	 * @param  array  $options    
	 * @param  array  $attributes 
	 * @return string             
	 */
	public function typeahead($name, array $options = array(), array $attributes = array())
	{
		$attributes = $this->default_attributes($name, $attributes);
		$model = isset($options['model']) ? $options['model'] : $this->object()->meta()->association($name)->foreign_model;

		$attributes['data-source'] = Arr::get($options, 'source', Tart::uri('typeahead').'?model='.$model);
		$attributes['data-provide'] = 'typeahead';

		$value = $this->object()->{$name};
		$value = $value instanceof Jam_Model ? $value->name() : $value;

		return Form::input($attributes['name'], $value, $attributes);
	}

	/**
	 * A widget to enter a belnogsto / hasone like association. 
	 * For displaying an exisiting item, you can use a template or a url. 
	 * If you use a template, it gets 'model', 'name', 'item' and 'id' as variables inside of it.
	 * If you use a 'url' option - it will use its response instead of a template, passing 'model', 'id' and 'name' as query parameter fillers. E.g. /admin/images/build?model={{model}}&id={{id}}
	 * 
	 * Options:
	 *  - model: string, defaults to the foreign_model of the association, can be comma separated
	 *  - template: string, the path for the view that is used to render an existing item
	 *  - url: string, a url to render an existing item
	 *  - container: the html id of the container tag
	 *  - source: string, the url used to retrieve the typeahead data. Defaults to the builtin typeahead action
	 *  - placeholder: the placeholder for the typeahead search input
	 *  
	 * @param  string $name       
	 * @param  array  $options    
	 * @param  array  $attributes 
	 * @return string             
	 */
	public function remoteselect($name, array $options = array(), array $attributes = array())
	{
		$attributes = $this->default_attributes($name, $attributes);

		return Tart::html($this, function($h, $self) use ($name, $options, $attributes) {

			$current = $self->object()->$name;
			$model = isset($options['model']) ? $options['model'] : $self->object()->meta()->association($name)->foreign_model;
			$template = Arr::get($options, 'template', 'tart/typeahead/remoteselect');

			if ( ! is_object($current) AND $current)
			{
				$current = Jam::find($model, $current);
			}

			$options = Arr::merge(array(
				'model' => $model,
				'container' => $attributes['id'].'_container',
				'source' => Tart::uri('typeahead').'?model='.$model,
				'url' => Tart::uri('typeahead', 'remoteselect_template').'?model={{model}}&name={{name}}&id={{id}}&template='.$template,
				'template' => $template,
			), $options);

			$options['url'] = strtr($options['url'], array('{{name}}' => $attributes['name']));

			$h('input', array('name' => $attributes['name'], 'type' => 'hidden', 'value' => ''));

			$h('input', array(
				'type' => 'text',
				'placeholder' => Arr::get($options, 'placeholder', 'Search for '.Arr::get($options, 'label', strtolower(Inflector::humanize($name)))),
				'class' => $current ? 'fade hide' : 'fade in',
				'data-provide' => 'remoteselect',
				'data-source' => $options['source'],
				'data-container' => '#'.$options['container'],
				'data-overwrite' => 1,
				'data-url' => $options['url'],
			));

			$h('span', array('id' => $options['container'], 'class' => 'remoteselect-container'), function($h, $self) use ($current, $model, $options) {
				if ($current)
				{
					$h->add(Request::factory(strtr($options['url'], array('{{model}}' => $model, '{{id}}' => Jam_Form::list_id($current))))->execute());
				}
			});

		})->render();	
	}

	/**
	 * A widget to enter collection associations.
	 *
	 * For adding new items you have two options. Using a typeahead to search for an existing item, or with a button to add a totaly new item (or both).
	 * To use either you can set up the 'new' option, which is a url for the action, used to render the new item.
	 * You will have 'model', 'name', 'count' and 'id' as query parameter fillers. E.g. /admin/images/build?model={{model}}&id={{id}}
	 * To display existing items, you'll use 'template' option, which gets 'name', 'item', 'form' and 'index' as variables
	 *
	 * Options:
	 *  - model: string, defaults to the foreign_model of the association, can be comma separated
	 *  - template: string, the path for the view that is used to render an existing item
	 *  - new: string, a url to render a new item
	 *  - new_button: string, the label of the "add new" button
	 *  - list: string, add a link to a list of all the elements in this multiselect
	 *  - source: string, the url used to retrieve the typeahead data. Defaults to the builtin typeahead action
	 *  - placeholder: the placeholder for the typeahead search input
	 *  - sortable: boolean, set to TRUE to enable sortable javascript plugin
	 * 
	 * @param  string $name       
	 * @param  array  $options    
	 * @param  array  $attributes 
	 * @return string             
	 */
	public function multiselect($name, array $options = array(), array $attributes = array())
	{
		$attributes = $this->default_attributes($name, $attributes);

		return Tart::html($this, function($h, $self) use ($name, $options, $attributes) {

			$model = isset($options['model']) ? $options['model'] : $self->object()->meta()->association($name)->foreign_model;
			$template = Arr::get($options, 'template', 'tart/typeahead/multiselect');

			$options = Arr::merge(array(
				'model' => $model,
				'container' => $attributes['id'].'_container',
				'source' => Tart::uri('typeahead').'?model='.$model,
				'new' => Tart::uri('typeahead', 'remoteselect_template').'?model='.$model.'&name={{name}}[]&id={{id}}&template='.$template,
				'new_button' => NULL,
				'search' => TRUE,
				'template' => $template,
				'list' => NULL,
				'sortable' => NULL,
				'label' => strtolower(Inflector::humanize($name)),
			), $options);

			$options['new'] = strtr($options['new'], array('{{name}}' => $attributes['name']));

			$h('input', array('name' => $attributes['name'].'[]', 'type' => 'hidden', 'value' => ''));

			$h('p', function($h) use ($name, $options) {
				
					$h('input', array(
						'type' => 'text',
						'placeholder' => Arr::get($options, 'placeholder', 'Search for '.$options['label']),
						'data-provide' => 'remoteselect',
						'data-source' => $options['source'],
						'data-container' => '#'.$options['container'],
						'data-url' => $options['new'],
						'style' => $options['search'] ? '' : 'display:none'
					));
			
				if ($options['new_button'])
				{
					$h('button', array('class' => 'btn', 'data-remoteselect-new' => $options['model']), $options['new_button']);
				}

				if ($options['list'])
				{
					$h('a', array('href' => $options['list'], 'class' => 'btn btn-link'), 'List '.$options['label']);
				}
			});

			$h('ul', array('class' => 'thumbnails', 'data-provide' => ($options['sortable'] ? 'sortable' : NULL), 'data-items' => '> li', 'data-tolerance' => 'pointer', 'data-placeholder' => 'sortable-placeholder thumbanil '.$options['sortable'], 'id' => $options['container']), function($h, $self) use ($name, $options, $attributes) {
				
				foreach ($self->object()->$name as $index => $item) 
				{
					$h->add(View::factory($options['template'], array('name' => $attributes['name'].'[]', 'item' => $item, 'index' => $index, 'form' => $self->fields_for($name, $index))));
				}
			});
		})->render();	
	}

	/**
	 * Radios select, bootstrap style
	 * @param  string $name       
	 * @param  array  $options    
	 * @param  array  $attributes 
	 * @return string             
	 */
	public function radios($name, array $options = array(), array $attributes = array())
	{
		$attributes = $this->default_attributes($name, $attributes);

		if ( ! isset($options['choices']))
			throw new Kohana_Exception('Radios tag widget requires a \'choices\' option');

		$choices = Jam_Form::list_choices($options['choices']);

		if ($blank = Arr::get($options, 'include_blank'))
		{
			Arr::unshift($choices, '', ($blank === TRUE) ? " -- Select -- " : $blank);
		}

		$radios = array();

		foreach ($choices as $key => $title)
		{
			$radio = $this->radio($name, array('value' => $key), array('id' => $attributes['id'].'_'.$key));
			$radios[] = new Builder_Html('label', array('class' => 'radio'),$radio.$title);
		}
		return '<div '.HTML::attributes($attributes).'>'.join("\n", $radios).'</div>';
	}

	/**
	 * An input to enter a url, when set display a link to that url alongside the input
	 * @param  string $name       
	 * @param  array  $options    
	 * @param  array  $attributes 
	 * @return string             
	 */
	public function url($name, array $options = array(), array $attributes = array())
	{
		$html = $this->input($name, $options, $attributes);
		if ($url = $this->object()->$name)
		{
			$domain = parse_url($url, PHP_URL_HOST);
			$html .= HTML::anchor($url, $domain.' <i class="icon-share-alt"></i>', array('class' => 'btn btn-link', 'title' => $url));
		}

		return $html;
	}
}
