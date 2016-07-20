<?php

require_once __DIR__.'/../vendor/autoload.php';

Kohana::modules(array(
	'database' => MODPATH.'database',
	'auth'     => MODPATH.'auth',
	'jam'      => __DIR__.'/../modules/jam',
	'jam-auth' => __DIR__.'/../modules/jam-auth',
	'jam-tart' => __DIR__.'/..',
));

function test_autoload($class)
{
	$file = str_replace('_', '/', $class);

	if ($file = Kohana::find_file('tests/classes', $file))
	{
		require_once $file;
	}
}

spl_autoload_register('test_autoload');

Kohana::$config
	->load('database')
		->set(Kohana::TESTING, array(
			'type'       => 'PDO',
			'connection' => array(
				'dsn'        => 'mysql:host=localhost;dbname=test-jam-tart',
				'username'   => 'root',
				'password'   => '',
				'persistent' => TRUE,
			),
			'table_prefix' => '',
			'charset'      => 'utf8',
			'caching'      => FALSE,
		));


Route::set('tart', 'admin(/<controller>(/<action>(/<id>)))')
	->defaults(array(
		'directory'  => 'tart',
		'controller' => 'dashboard',
		'action'     => 'index',
	));

Route::set('tart_category', 'admin/<controller>/category/<category>(/<action>(/<id>))')
	->defaults(array(
		'directory'  => 'tart',
		'controller' => 'dashboard',
		'action'     => 'index',
	));

Kohana::$base_url = '/';
Kohana::$index_file = FALSE;
HTML::$windowed_urls = TRUE;

Kohana::$environment = Kohana::TESTING;

Request::factory();