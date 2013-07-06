<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Jamtart_HtmlTest 
 *
 * @group jam-tart
 * @group jam-tart.html
 * 
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
class Jamtart_HtmlTest extends PHPUnit_Framework_TestCase {


	public function data_tab()
	{
		return array(
			array('one', 'two', ' id="two" class="tab-pane fade " disabled="disabled"'),
			array('one', 'one', ' id="one" class="tab-pane fade active in"'),
		);
	}
	
	/**
	 * @dataProvider data_tab
	 */
	public function test_tab($current, $id, $expected)
	{
		$this->assertEquals($expected, Tart_Html::tab($current, $id));
	}
	

	public function test_navigation()
	{
		$base = Tart::uri();

		$user = Jam::build('user')->load_fields(array(
			'id' => 1,
			'roles' => array(
				array('id' => 1, 'allowed' => array($base.'/*'), 'disallowed' => array($base.'/cities*'))
			)
		));

		$navigation = array(
			'users' => 'Users',
			'Group' => array(
				'cities' => 'Cities',
				'countries' => 'Countries',
			)
		);

		$rendered = (string) Tart_Html::navigation($navigation, $user, 'users');

		$this->assertSelectEquals('.navbar > .navbar-inner ul.nav > li.active > a', 'Users', TRUE, $rendered);
		$this->assertSelectEquals('.navbar > .navbar-inner ul.nav > li.dropdown > a', 'Group', TRUE, $rendered);
		$this->assertSelectEquals('.navbar > .navbar-inner ul.nav > li.dropdown > ul.dropdown-menu > li > a', 'Countries', TRUE, $rendered);
		$this->assertSelectEquals('.navbar > .navbar-inner ul.nav > li.dropdown > ul.dropdown-menu > li > a', 'Cities', FALSE, $rendered);
	}

	public function test_submenu()
	{
		$rendered = (string) Tart_Html::submenu('feed', 'shop', array('shop' => 'Shop', 'magazine' => 'Magazine'));

		$this->assertSelectEquals('.nav > li.active > a', 'Shop', TRUE, $rendered);
		$this->assertSelectEquals('.nav > li > a', 'Magazine', TRUE, $rendered);
	}

	public function data_anchor()
	{
		$base = Tart::uri();

		return array(
			array($base.'/users', 'Users', array(), '<a href="'.$base.'/users">Users</a>'),
			array($base.'/cities', 'Cities', array(), NULL),
			array($base.'/users', 'Users', array('if' => TRUE), '<a href="'.$base.'/users">Users</a>'),
			array($base.'/users', 'Users', array('if' => FALSE), '<span>Users</span>'),
			array($base.'/users', 'Users', array('unless' => FALSE, 'class' => 'test'), '<a href="'.$base.'/users" class="test">Users</a>'),
			array('http://test.example.com', 'Test', array('external' => TRUE), '<a href="http://test.example.com" target="_blank">Test <i class="icon-share"></i></a>'),
		);
	}
	
	/**
	 * @dataProvider data_anchor
	 */
	public function test_anchor($url, $title, $attributes, $expected)
	{
		$base = Tart::uri();
		
		$user = Jam::build('user')->load_fields(array(
			'id' => 1,
			'roles' => array(
				array('id' => 1, 'allowed' => array($base.'/*'), 'disallowed' => array($base.'/cities*'))
			)
		));

		$this->assertEquals($expected, Tart_Html::anchor($url, $title, $attributes, $user));
	}

	public function data_date_span()
	{
		return array(
			array('2012-01-01', '2012-01-02', '24 hours ago'),
			array('2012-01-01', '2012-01-04', '3 days ago'),
			array('2012-01-01', '2012-03-04', '2 months ago'),
			array('2012-01-01', '2011-01-01', 'in 11 months'),
		);
	}
	
	/**
	 * @dataProvider data_date_span
	 */
	public function test_date_span($time, $local, $expected)
	{
		$this->assertEquals($expected, Tart_Html::date_span(strtotime($time), strtotime($local)));
	}
	
	
}