<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Jamtart_Tart_RequestTest 
 *
 * @group jam-tart
 * @group jam-tart.request
 * 
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
class Jamtart_RequestTest extends PHPUnit_Framework_TestCase {

	public function test_fix_files()
	{
		$files = array(
			'images' => array(
				'name' => array(
					'0' => array(
						'file' => 'test1.jpg',
					),
					'1' => 'test2.jpg',
					'2' => 'test3.jpg',
					'3' => 'test4.png',
				),
				'type' => array(
					'0' => array(
						'file' => 'image/jpeg',
					),
					'1' => 'image/jpeg',
					'2' => 'image/jpeg',
					'3' => 'image/png',
				),
				'tmp_name' => array(
					'0' => array(
						'file' => '/tmp/n1l54Gs',
					),
					'1' => '/tmp/n2l54Gs',
					'2' => '/tmp/n3l54Gs',
					'3' => '/tmp/n4l54Gs',
				),
				'error' => array(
					'0' => array(
						'file' => '0',
					),
					'1' => '0',
					'2' => '0',
					'3' => '0',
				),
				'size' => array(
					'0' => array(
						'file' => '1715',
					),
					'1' => '1715',
					'2' => '1715',
					'3' => '1715',
				),
			),
		);

		$expected = array(
			'images' => array(
				'0' => array(
					'file' => array(
						'name' => 'test1.jpg',
						'type' => 'image/jpeg',
						'tmp_name' => '/tmp/n1l54Gs',
						'error' => '0',
						'size' => '1715',
					)
				),
				'1' => array(
					'name' => 'test2.jpg',
					'type' => 'image/jpeg',
					'tmp_name' => '/tmp/n2l54Gs',
					'error' => '0',
					'size' => '1715',
				),
				'2' => array(
					'name' => 'test3.jpg',
					'type' => 'image/jpeg',
					'tmp_name' => '/tmp/n3l54Gs',
					'error' => '0',
					'size' => '1715',
				),
				'3' => array(
					'name' => 'test4.png',
					'type' => 'image/png',
					'tmp_name' => '/tmp/n4l54Gs',
					'error' => '0',
					'size' => '1715',
				)
			)
		);

		$this->assertEquals($expected, Tart_Request::fix_files($files));
	}
	

	public function test_post()
	{
		$files = array(
			'images' => array(
				'name' => array(
					'0' => array(
						'file' => 'test1.jpg',
					),
				),
				'type' => array(
					'0' => array(
						'file' => 'image/jpeg',
					),
				),
				'tmp_name' => array(
					'0' => array(
						'file' => '/tmp/n1l54Gs',
					),
				),
				'error' => array(
					'0' => array(
						'file' => '0',
					),
				),
				'size' => array(
					'0' => array(
						'file' => '1715',
					),
				),
			),
		);

		$post = array(
			'images' => array(
				'0' => array(
					'name' => 'My Name',
				)
			)
		);

		$post = Tart_Request::post($post, $files);

		$expected = array(
			'images' => array(
				'0' => array(
					'file' => array(
						'name' => 'test1.jpg',
						'type' => 'image/jpeg',
						'tmp_name' => '/tmp/n1l54Gs',
						'error' => '0',
						'size' => '1715',
					),
					'name' => 'My Name',
				)
			)
		);

		$this->assertEquals($expected, $post);
	}

	public function data_modified_params()
	{
		return array(
			array(
				array(
					'name' => 'test',
					'subtitle' => 'sub',
					'odd_param' => '__clear',
				),
				array('name', 'subtitle', 'odd_param'),
				array(
					'name' => 'test',
					'subtitle' => 'sub',
					'odd_param' => NULL
				),
			),
			array(
				array(
					'name' => 'my name',
					'categories' => array(
						'0' => '10',
						'1' => '16',
					),
					'folder' => array(
						'ideas' => '__clear',
						'colors' => array(10, 20),
						'name' => NULL,
						'title' => 'Test'
					),
					'featured' => '',
					'subtitle' => '__clear',
					'odd_param' => '__clear',
				),
				array('subtitle', 'categories', 'featured', 'folder' => array('ideas', 'colors', 'name')),
				array(
					'categories' => array(
						'0' => '10',
						'1' => '16',
					),
					'folder' => array(
						'ideas' => NULL,
						'colors' => array(10, 20),
					),
					'subtitle' => NULL,
				),
			),

		);
	}
	
	/**
	 * @dataProvider data_modified_params
	 */
	public function test_modified_params($params, $expected_params, $expected_result)
	{
		$this->assertEquals($expected_result, Tart_Request::modified_params($params, $expected_params));
	}

	public function test_to_modifications()
	{
		$data = array('name' => 'Parent', 'test' => NULL, 'folder' => array('name' => 'Best', 'subtitle' => NULL));
		$expected = 'name set to "Parent", test cleared and folder: name set to "Best" and subtitle cleared';
		
		$this->assertEquals($expected, Tart_Request::to_modifications($data));
	}

}
