<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Jamtart_ColumnTest 
 *
 * @group jam-tart
 * @group jam-tart.column
 * 
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
class Jamtart_ColumnTest extends PHPUnit_Framework_TestCase {

	public $city;
	public $country;

	public function setUp()
	{
		parent::setUp();
		$this->country = Jam::build('test_country')->load_fields(array(
			'id' => 1,
			'name' => 'Freeland',
		));

		$this->city = Jam::build('test_city')->load_fields(array(
			'id' => 1,
			'population' => 200000,
			'name' => 'Bigville',
			'lat' => 20,
			'lon' => 43.212,
			'is_big' => TRUE,
			'has_service' => FALSE,
			'data' => array('some', 'stuff'),
			'description' => 'Really really big and coprehansive text is this bigville description. It should be considered for limiting a bit',
			'created_at' => strtotime('2000-01-01 14:30:00'),
			'cover' => 'image.jpg',
			'url' => 'http://example.com/ths-is-a-very-lon-url/and-should-be-shortend',
			'country' => $this->country,
		));
	}

	public function test_default_fields()
	{
		$column = new Tart_Column();
		$column->item($this->city);
		$rendered = $column->name('name')->render();
		$this->assertEquals('Bigville', $rendered);

		$rendered = $column->name('population')->render();
		$this->assertEquals('200,000', $rendered);

		$rendered = $column->name('lat')->render();
		$this->assertEquals('20.00', $rendered);

		$rendered = $column->name('lon')->render();
		$this->assertEquals('43.21', $rendered);

		$rendered = $column->name('is_big')->render();
		$this->assertEquals('<i class="icon-ok"></i>', $rendered);

		$rendered = $column->name('has_service')->render();
		$this->assertEquals('', $rendered);

		$rendered = $column->name('data')->render();
		$this->assertEquals(
'<pre class="debug"><small>array</small><span>(2)</span> <span>(
    0 => <small>string</small><span>(4)</span> "some"
    1 => <small>string</small><span>(5)</span> "stuff"
)</span></pre>', $rendered);

		$rendered = $column->name('description')->render();
		$this->assertEquals('Really really big and coprehansive&nbsp;text…', $rendered);

		$rendered = $column->name('created_at')->render();
		$this->assertEquals('<span title="1 Jan 2000">13 years ago</span>', $rendered);

		$rendered = $column->name('url')->render();
		$this->assertEquals('http://example.com/ths-is-a-ve…&nbsp;<a href="http://example.com/ths-is-a-very-lon-url/and-should-be-shortend" target="_blank"><i class="icon-share-alt"></i></a>', $rendered);

		$rendered = $column->name('cover')->render();
		$this->assertEquals('<img src="/upload/test_city/1/1/image.jpg" alt="Bigville" class="img-polaroid" />', $rendered);
	}

	public function test_callback()
	{
		$column = new Tart_Column(function($city, $name){
			return number_format($city->{$name}).' people';
		});

		$rendered = $column->name('population')->item($this->city)->render();
		$this->assertEquals('200,000 people', $rendered);
	}

	public function test_default_associations()
	{
		$column = new Tart_Column();
		$rendered = $column->name('country')->item($this->city)->render();

		$this->assertEquals($this->country->name(), $rendered);
	}

	public function test_set_filter()
	{
		$column = new Tart_Column();
		$filter = new Tart_Filter(array());
		$table = new Tart_Table(Jam::all('test_city'));
		$column->item($this->city);
		$column->set_filter($filter, 'area');
		$rendered = (string) $column->name('name')->item($this->city)->render();

		$this->assertSelectEquals('span[data-provide="filters"][draggable="true"][data-dropzone=".tart-filter"', 'Bigville', TRUE, $rendered);
	}
}