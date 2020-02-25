<?php defined('SYSPATH') OR die('No direct script access.');

use PHPUnit\Framework\TestCase;

/**
 * Jamtart_CoreTest 
 *
 * @group jam-tart
 * @group jam-tart.core
 * 
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
class Jamtart_CoreTest extends TestCase {

	public function test_index()
	{
		$collection = Jam::all('test_city')->load_fields(array('id' => 1, 'name' => 'London'));
		$items = array('name' => Tart::column());
		$index = Tart::index($collection, 0, $items);

		$this->assertInstanceOf('Tart_Index', $index);
		$this->assertSame($collection, $index->collection());
		$this->assertSame($items, $index->content()->items());
	}

	public function test_entry()
	{
		$callback = function($item){ };

		$entry = Tart::entry('search', NULL, $callback);
		$this->assertInstanceOf('Tart_Filter_Entry_Search', $entry);
		$this->assertSame($callback, $entry->callback());

		$choices = array('1', '2');
		$entry = Tart::entry('select', $choices);
		$this->assertInstanceOf('Tart_Filter_Entry_Select', $entry);
		$this->assertSame($choices, $entry->params());
	}

	public function test_column()
	{
		$column = Tart::column();
		$this->assertInstanceOf('Tart_Column', $column);

		$callback = function($item){ };

		$column = Tart::column($callback);
		$this->assertInstanceOf('Tart_Column', $column);
		$this->assertSame($callback, $column->callback());

		$column = Tart::column('test', $callback);
		$this->assertInstanceOf('Tart_Column_Test', $column);
		$this->assertSame($callback, $column->callback());
	}

	public function test_filter()
	{
		$data = array('name' => 1);
		$items = array('name' => Tart::entry('search'));
		$index = Tart::filter($data, $items);

		$this->assertInstanceOf('Tart_Filter', $index);
		$this->assertEquals($data, $index->data());
		$this->assertEquals($items, $index->items());
	}

	public function test_table()
	{
		$collection = Jam::all('test_city')->load_fields(array('id' => 1, 'name' => 'London'));
		$items = array('name' => Tart::column());
		$table = Tart::table($collection, $items);

		$this->assertInstanceOf('Tart_Table', $table);
		$this->assertEquals($collection, $table->collection());
		$this->assertEquals($items, $table->items());
	}

	public function data_to_sentence()
	{
		return array(
			array(array(), ''),
			array(array('one'), 'one'),
			array(array('one', 'two'), 'one and two'),
			array(array('one', 'two', 'three', 'four'), 'one, two, three and four'),
		);
	}
	
	/**
	 * @dataProvider data_to_sentence
	 */
	public function test_to_sentence($array, $expected_string)
	{
		$this->assertEquals($expected_string, Tart::to_sentence($array));
	}
	

	public function data_convert_to_class()
	{
		return array(
			array('test_model', 'Test_Model'),
			array('column_name_big', 'Column_Name_Big'),
		);
	}
	
	/**
	 * @dataProvider data_convert_to_class
	 */
	public function test_convert_to_class($name, $expected_class)
	{
		$this->assertEquals($expected_class, Tart::convert_to_class($name));
	}

	public function data_allowed()
	{
		$base = Tart::uri();

		return array(
			array($base.'/stats', array($base.'/stats*'), array(), TRUE),
			array($base.'/stats', array($base.'/*'), array(), TRUE),
			array($base.'/users', array($base.'/stats*'), array(), FALSE),
			array($base.'/stats', array($base.'/cities*', $base.'/countries*'), array(), FALSE),
			array($base.'/stats', array($base.'/*'), array($base.'/stats*'), FALSE),
		);
	}
	
	/**
	 * @dataProvider data_allowed
	 */
	public function test_allowed($uri, $allowed, $disallowed, $expected)
	{
		$this->assertEquals($expected, Tart::allowed($uri, $allowed, $disallowed));
	}

	public function test_user_allowed()
	{
		$base = Tart::uri();

		$user = Jam::build('user', array(
			'roles' => array(
				array('name' => 'login'),
				array('name' => 'admin', 'allowed' => array($base.'/*')),
			)
		));
		
		$guest = Jam::build('user', array(
			'roles' => array(
				array('name' => 'login'),
				array('name' => 'guest', 'allowed' => array($base.'/stats*')),
			)
		));

		$this->assertEquals(TRUE, Tart::user_allowed($base.'/stats', NULL));
		$this->assertEquals(TRUE, Tart::user_allowed($base.'/stats', $user));
		$this->assertEquals(TRUE, Tart::user_allowed($base.'/users', $user));
		$this->assertEquals(TRUE, Tart::user_allowed($base.'/stats', $guest));
		$this->assertEquals(FALSE, Tart::user_allowed($base.'/users', $guest));
	}
	

	public function test_uri()
	{
		$base = Tart::uri();

		$city_loaded = Jam::build('test_city')->load_fields(array('id' => 1, 'name' => 'test_city'));
		$city_new = Jam::build('test_city');

		$uri = Tart::uri($city_loaded);
		$this->assertEquals($base.'/test_cities/edit/1', $uri);

		$uri = Tart::uri($city_loaded, 'delete');
		$this->assertEquals($base.'/test_cities/delete/1', $uri);

		$uri = Tart::uri($city_loaded, array('action' => 'build', 'id' => 5));
		$this->assertEquals($base.'/test_cities/build/5', $uri);		

		$uri = Tart::uri($city_loaded, array('action' => 'build', 'controller' => 'tests'));
		$this->assertEquals($base.'/tests/build/1', $uri);

		$uri = Tart::uri($city_new, array('action' => 'build', 'id' => 5));
		$this->assertEquals($base.'/test_cities/build/5', $uri);

		$uri = Tart::uri('test_cities');
		$this->assertEquals($base.'/test_cities', $uri);

		$uri = Tart::uri('test_cities', 'index');
		$this->assertEquals($base.'/test_cities', $uri);

		$uri = Tart::uri('test_cities', array('action' => 'remove', 'id' => 1));
		$this->assertEquals($base.'/test_cities/remove/1', $uri);
	}
	
}
