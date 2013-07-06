<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Generate a tart controller for a given model
 *
 * options:
 *
 *  - model: required, the jam model name
 *  - module: default 'admin', generate the classes and views in this module
 *  - controller: defaults to plural model name - set custom controller if nesessary
 *  - author: author of the class, defaults to Ivan Kerin
 *  - batch_delete: boolean flag - set this to include code for batch delete
 *  - batch_modify: boolean flat - set this to include code for batch modify
 *  - force: boolean flat - overwrite existing files
 *
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
class Task_Tart_Generate extends Minion_Task {

	protected $_options = array(
		'model' => FALSE, 
		'module' => 'admin', 
		'author' => 'Ivan Kerin', 
		'controller' => FALSE,
		'batch_delete' => FALSE,
		'force' => FALSE,
		'unlink' => FALSE,
		'batch_modify' => FALSE,
	);

	public function build_validation(Validation $validation)
	{
		return parent::build_validation($validation)
			->rule('module', 'not_empty')
			->rule('module', 'in_array', array(':value', array_keys(Kohana::modules())))
			->rule('model', 'not_empty')
			->rule('model', 'Jam::meta');
	}

	protected function _execute(array $options)
	{
		$module_name = $options['module'];
		$dir = Arr::get(Kohana::modules(), $module_name);
		$meta = Jam::meta($options['model']);
		$controller = $options['controller'] ?: Inflector::plural($meta->model());
		$item_name = $meta->model();
		$item_title = ucwords(Inflector::humanize($meta->model()));
		$plural_name = str_replace('_', ' ', $controller);
		$author = $options['author'];

		$controller_option = $options['controller'] ? '->controller(\''.$controller.'\')' : NULL;

		$controller_title = Jam::capitalize_class_name($plural_name);
		$controller_path = str_replace('_', DIRECTORY_SEPARATOR, $controller_title);

		$controller_dir = $dir.'classes'.DIRECTORY_SEPARATOR.'Controller'.DIRECTORY_SEPARATOR.'Tart';
		$controller_file = $controller_dir.DIRECTORY_SEPARATOR.$controller_path.EXT;
		$controller_class ='Controller_Tart_'.str_replace(' ', '_', $controller_title);

		$views_path = 'tart'.DIRECTORY_SEPARATOR.strtolower($controller_path);
		$views_dir = $dir.'views'.DIRECTORY_SEPARATOR.$views_path.DIRECTORY_SEPARATOR;

		$extra_controller = $options['controller'] ? ", array('controller' => '{$controller}')" : '';
		$extra_controller_delete = $options['controller'] ? "array('controller' => '{$controller}', 'action' => 'delete')" : "'delete'";

		$batch_index = NULL;
		$batch_delete = NULL;
		$batch_modify = NULL;

		if ( ! is_dir($views_dir))
		{
			mkdir($views_dir, 0777, TRUE);
		}

		if ( ! is_dir(dirname($controller_file)))
		{
			mkdir(dirname($controller_file), 0777, TRUE);
		}

		if ($options['batch_delete'] !== FALSE OR $options['batch_modify'] !== FALSE)
		{
			$actions = '';
			if ($options['batch_delete'] !== FALSE)
			{
				$actions .= "\n				'delete' => 'Delete',";

				$batch_delete = <<<BATCH_DELETE
	public function batch_delete(\$ids)
	{
		if (\$this->request->method() == Request::POST)
		{
			\$result = array();
			foreach (Jam::all('{$meta->model()}')->where_key(\$ids) as \${$item_name}) 
			{
				\$result[] = \${$item_name}->delete();
			}
			\$this->notify('success', count(\$result).' {$plural_name} deleted');
			\$this->redirect(Tart::uri('{$controller}'));
		}
		else
		{
			\$table = Tart::table(Jam::all('{$meta->model()}')->where_key(\$ids))
				->selected(\$ids)
				->columns('{$meta->name_key()}', Tart::column());

			\$this->template->content = View::factory('{$views_path}/batch_delete', array('table' => \$table));
		}
	}

BATCH_DELETE;
			}

			if ($options['batch_modify'] !== FALSE)
			{
				$actions .= "\n				'modify' => 'Modify',";

				$batch_modify = <<<BATCH_MODIFY
	public function batch_modify(\$ids)
	{
		\$params = array('{$meta->name_key()}');

		if (\$this->request->method() == Request::POST)
		{
			\$modified = Tart_Request::modified_params(\$this->request->post(), \$params);

			foreach (Jam::all('{$meta->model()}')->where_key(\$ids) as \$item) 
			{
				\$item->set(\$modified)->save();
			}

			\$this->notify('success', count(\$ids).' ${plural_name} modified: '.Tart_Request::to_modifications(\$modified));
			\$this->redirect(Tart::uri('${controller}'));
		}
		else
		{
			\$table = Tart::table(Jam::all('{$meta->model()}')->where_key(\$ids))
				->selected(\$ids)
				->columns('{$meta->name_key()}', Tart::column());

			\$item = Jam::build('{$meta->model()}', Jam_Form::common_params(\$table->collection(), \$params));

			\$this->template->content = View::factory('{$views_path}/batch_modify', array('table' => \$table, 'form' => Jam::form(\$item, 'tart_general')->validation(FALSE)));
		}
	}

BATCH_MODIFY;
			}

			$batch_index = <<<BATCH_INDEX
			->batch_actions(array({$actions}
			))
BATCH_INDEX;

		}

		$controller_content = <<<CONTROLLER
<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Tart Controller: $controller_title
 *
 * @package $module_name
 * @author $author
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
class {$controller_class} extends Controller_Tart_Layout {

	public function action_index()
	{
		\$collection = Jam::all('{$meta->model()}');

		\$filter = Tart::filter(\$this->request->query())
			{$controller_option}
			->entries(array(
				'q' => Tart::entry('search'),
			))
			->apply(\$collection);

		\$index = Tart::index(\$collection, \$this->request->query('offset'))
			{$controller_option}
{$batch_index}
			->columns(array(
				'{$meta->name_key()}' => Tart::column(),
				'actions' => Tart::column('actions'),
			));

		\$this->template->set(array(
			'content' => View::factory('{$views_path}/index', array('index' => \$index, 'filter' => \$filter)),
			'sidebar' => View::factory('{$views_path}/sidebar', array('filter' => \$filter)),
		));
	}

	public function action_edit()
	{
		\$item = Jam::find_insist('{$meta->model()}', \$this->request->param('id'));

		if (\$this->request->method() === Request::POST AND \$item->set(\$this->post())->check())
		{
			\$this->notify('success', '{$item_title} Updated');
			\$item->save();
		}
		\$this->template->content = View::factory('{$views_path}/edit', array('item' => \$item));
	}

	public function action_new()
	{
		\$item = Jam::build('{$meta->model()}');

		if (\$this->request->method() === Request::POST AND \$item->set(\$this->post())->check())
		{
			\$item->save();
			\$this->notify('success', '{$item_title} Created');
			\$this->redirect(Tart::uri(\$item{$extra_controller}));
		}
		\$this->template->content = View::factory('{$views_path}/new', array('item' => \$item));
	}

	public function action_delete()
	{
		\$item = Jam::find_insist('{$meta->model()}', \$this->request->param('id'));
		\$item->delete();
		\$this->notify('success', "{$item_title} \{\$item->name()\} deleted");
		\$this->redirect(Tart::uri('{$controller}'));
	}

{$batch_delete}
{$batch_modify}
}
CONTROLLER;
	
		Minion_Jam_Generate::modify_file($controller_file, $controller_content, $options['force'] !== FALSE, $options['unlink'] !== FALSE);

		$index_file = $views_dir.'index'.EXT;

		$index_content = <<<VIEW_INDEX
<ul class="breadcrumb">
	<li class="active">
		{$controller_title} <?php echo \$filter->render_active(); ?>
	</li>
	<li class="pull-right">
		<?php echo \$index->pagination()->pager(); ?>
	</li>
</ul>
<?php echo \$index->render(); ?>
VIEW_INDEX;

		Minion_Jam_Generate::modify_file($index_file, $index_content, $options['force'] !== FALSE, $options['unlink'] !== FALSE);

		$sidebar_file = $views_dir.'sidebar'.EXT;

		$sidebar_content = <<<VIEW_SIDEBAR
<ul class="nav nav-tabs nav-stacked">
	<li>
		<?php echo Tart_Html::anchor(Tart::uri(Jam::build('{$meta->model()}'){$extra_controller}), '<i class="icon-plus"></i> Add {$item_title}'); ?>
	</li>
</ul>
<?php echo \$filter->render() ?>
VIEW_SIDEBAR;

		Minion_Jam_Generate::modify_file($sidebar_file, $sidebar_content, $options['force'] !== FALSE, $options['unlink'] !== FALSE);

		$new_file = $views_dir.'new'.EXT;
		$new_content = <<<VIEW_NEW
<ul class="breadcrumb">
	<li>
		<?php echo Tart_Html::anchor(Tart::uri('{$controller}'), '{$controller_title}'); ?>
		<span class="divider">/</span>
	</li>
	<li class="active">
		Create New {$item_title}
	</li>
</ul>
<?php echo Form::open(Tart::uri(\$item{$extra_controller}), array('class' => 'form-horizontal', 'enctype' => 'multipart/form-data')) ?>
	<?php echo View::factory('{$views_path}/form', array('item' => \$item)) ?>
	<div class="form-actions">
		<?php echo Tart_Html::anchor(Tart::uri('{$controller}'), 'Cancel', array('class' => 'btn btn-link')); ?>
		<button type="submit" class="btn btn-primary">Create {$item_title}</button>
	</div>
<?php echo Form::close() ?>
VIEW_NEW;

		Minion_Jam_Generate::modify_file($new_file, $new_content, $options['force'] !== FALSE, $options['unlink'] !== FALSE);

		$edit_file = $views_dir.'edit'.EXT;

		$edit_content = <<<VIEW_EDIT
<ul class="breadcrumb">
	<li>
		<a href="<?php echo Tart::uri('{$controller}') ?>">{$controller_title}</a>
		<span class="divider">/</span>
	</li>
	<li class="active">
		Edit {$item_name} <strong><?php echo \$item->name() ?></strong>
	</li>
</ul>
<?php echo Form::open(Tart::uri(\$item{$extra_controller}), array('class' => 'form-horizontal', 'enctype' => 'multipart/form-data')) ?>
	<?php echo View::factory('{$views_path}/form', array('item' => \$item)) ?>
	<div class="form-actions">
		<?php echo Tart_Html::anchor(Tart::uri('{$controller}'), 'Cancel', array('class' => 'btn btn-link')); ?>
		<button type="submit" class="btn btn-primary">Save changes</button>

		<?php echo Tart_Html::anchor(Tart::uri(\$item, {$extra_controller_delete}), 'Delete {$item_title}', array('class' => 'btn btn-danger pull-right', 'data-confirm' => 'Are you sure you want to delete this {$item_title}?')); ?>
	</div>
<?php echo Form::close() ?>
VIEW_EDIT;

		Minion_Jam_Generate::modify_file($edit_file, $edit_content, $options['force'] !== FALSE, $options['unlink'] !== FALSE);

		$form_file = $views_dir.'form'.EXT;

		$form_content = <<<VIEW_FORM
<?php \$form = Jam::form(\$item, 'tart_general') ?>
<fieldset>
	<legend>Information</legend>
	<?php echo \$form->row('input', '{$meta->name_key()}') ?>
</fieldset>
VIEW_FORM;

		Minion_Jam_Generate::modify_file($form_file, $form_content, $options['force'] !== FALSE, $options['unlink'] !== FALSE);

		if ($options['batch_delete'] !== FALSE)
		{
			$batch_delete_file = $views_dir.'batch_delete'.EXT;
			$batch_delete_content = <<<VIEW_BATCH_DELETE
<ul class="breadcrumb">
	<li>
		<?php echo Tart_Html::anchor(Tart::uri('{$controller}'), '{$controller_title}'); ?>
		<span class="divider">/</span>
	</li>
	<li class="active">
		Batch Delete
	</li>
</ul>
<?php echo Form::open(Tart::uri('{$controller}', 'batch'), array('class' => 'form-horizontal', 'enctype' => 'multipart/form-data')) ?>
	<?php echo \$table->render() ?>
	<div class="form-actions">
		<?php echo Tart_Html::anchor(Tart::uri('{$controller}'), 'Cancel', array('class' => 'btn btn-link')); ?>

		<button type="submit" class="btn btn-primary" name="action" value="delete">Delete Selected</button>
	</div>
<?php echo Form::close() ?>
VIEW_BATCH_DELETE;

			Minion_Jam_Generate::modify_file($batch_delete_file, $batch_delete_content, $options['force'] !== FALSE, $options['unlink'] !== FALSE);
		}

		if ($options['batch_modify'] !== FALSE)
		{
			$batch_modify_file = $views_dir.'batch_modify'.EXT;
			$batch_modify_content = <<<VIEW_BATCH_MODIFY
<ul class="breadcrumb">
	<li>
		<?php echo Tart_Html::anchor(Tart::uri('{$controller}'), '{$controller_title}'); ?>
		<span class="divider">/</span>
	</li>
	<li class="active">
		Batch Modify
	</li>
</ul>
<?php echo Form::open(Tart::uri('{$controller}', 'batch'), array('class' => 'form-horizontal', 'enctype' => 'multipart/form-data')) ?>
	<?php echo \$table->render() ?>
	<fieldset>
		<legend>Modify Selected Items</legend>
		<?php echo \$form->row('input', '{$meta->name_key()}', array('clear' => TRUE)); ?>
	</fieldset>
	<div class="form-actions">
		<?php echo Tart_Html::anchor(Tart::uri('{$controller}'), 'Cancel', array('class' => 'btn btn-link')); ?>

		<button type="submit" class="btn btn-primary" name="action" value="modify">Modify Selected Items</button>
	</div>
<?php echo Form::close() ?>
VIEW_BATCH_MODIFY;

			Minion_Jam_Generate::modify_file($batch_modify_file, $batch_modify_content, $options['force'] !== FALSE, $options['unlink'] !== FALSE);
		}
	}
}