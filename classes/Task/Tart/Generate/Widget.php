<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Generate a tart controller for a given model
 *
 * options:
 *
 *  - name: required, the name of the widget, eg total_userscount
 *  - type: string - number, chart
 *  - module: default 'admin', generate the stats widget class
 *  - force: boolean flat - overwrite existing files
 *
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
class Task_Tart_Generate_Widget extends Minion_Task {

	protected $_options = array(
		'name' => FALSE, 
		'type' => FALSE, 
		'module' => 'admin', 
		'author' => 'Ivan Kerin', 
		'force' => FALSE,
	);

	public function build_validation(Validation $validation)
	{
		return parent::build_validation($validation)
			->rule('module', 'not_empty')
			->rule('module', 'in_array', array(':value', array_keys(Kohana::modules())))
			->rule('name', 'not_empty')
			->rule('type', 'in_array', array(':value', array('number', 'chart')));
	}

	protected function _execute(array $options)
	{
		$module_name = $options['module'];
		$module_dir = Arr::get(Kohana::modules(), $module_name);
		
		$author = $options['author'];

		switch ($options['type']) 
		{
			case 'number':
				$parent = 'Stats_Widget_Number';
			break;
			case 'chart':
				$parent = 'Stats_Widget_Chart';
			break;
			
			default:
				$parent = 'Stats_Widget';
		}

		$name = $options['name'];
		$title = Jam::capitalize_class_name(str_replace('_', ' ', $name));
		$path = str_replace('_', DIRECTORY_SEPARATOR, $title);

		$dir = $module_dir.'classes'.DIRECTORY_SEPARATOR.'Stats'.DIRECTORY_SEPARATOR.'Widget';
		$file = $dir.DIRECTORY_SEPARATOR.$path.EXT;
		$class ='Stats_Widget_'.str_replace(' ', '_', $title);

		if ( ! is_dir(dirname($file)))
		{
			mkdir(dirname($file), 0777, TRUE);
		}

		$widget_content = <<<WIDGET
<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Stats Widget: $title
 *
 * @package $module_name
 * @author $author
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
class {$class} extends {$parent} {

	protected \$_title = '{$title}';

	public function retrieve(\$start_date, \$end_date)
	{
		// return 0;
	}
}
WIDGET;
	
		Minion_Jam_Generate::modify_file($file, $widget_content, $options['force'] !== FALSE);
	}
}