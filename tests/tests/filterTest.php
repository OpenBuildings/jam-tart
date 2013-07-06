<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Jamtart_FiltersTest 
 *
 * @group jam-tart
 * @group jam-tart.filter
 * 
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
class Jamtart_FilterTest extends PHPUnit_Framework_TestCase {

	public function test_apply()
	{
		$collection = Jam::all('test_city');
		$data = array('test' => '1');
		$filter = new Tart_Filter($data);
		$self = $this;
		$filter_entry = Tart::entry('search', NULL, function($collection, $value) use ($self){
			$self->assertEquals('1', $value);
			$collection->where('name', '=', $value);
			return 'Active '.$value;
		});
		$filter->entries('test', $filter_entry);

		$filter->apply($collection);

		$this->assertEquals('SELECT `test_cities`.* FROM `test_cities` WHERE `test_cities`.`name` = \'1\'', (string) $collection);
		$this->assertEquals('Active 1', $filter->entries('test')->active());
	}


	public function test_render_active()
	{
		$filter = new Tart_Filter(array('name' => 'nn', 'test' => 'ttt'));

		$filter->entries(array(
			'name' => Tart::entry('search', NULL, function(){
				return 'Name Active';
			}),
			'test' => Tart::entry('search', NULL, function(){
				return 'Test Active';
			}),
		));

		$filter->apply(Jam::all('test_city'));

		$base = Tart::uri();

		$this->assertEquals('<a href="'.$base.'/test_cities?test=ttt" class="label">Name Active</a> and <a href="'.$base.'/test_cities?name=nn" class="label">Test Active</a>', $filter->render_active());
	}

	public function test_render()
	{
		$filter = new Tart_Filter(array('name' => 'nn', 'test' => 'ttt'));

		$filter->entries(array(
			'name' => Tart::entry('search', NULL, function(){
				return 'Name Active';
			}),
			'test' => Tart::entry('select', array('1' => 'test 1', '2' => 'test 2'), function(){
				return 'Test Active';
			}),
		));

		$filter->apply(Jam::all('test_city'));

		$base = Tart::uri();

		$expected = <<<HTML
<form action="{$base}/test_cities" method="GET" class="tart-filter" enctype="multipart/form-data">
  <div class="control-group control-group-input">
  <label class="control-label" for="name">Search</label>
  <div class="controls">
<input type="text" id="name" name="name" value="nn" tabindex="1" class="search" />
    
  </div>
</div>
  <div class="control-group control-group-select">
  <label class="control-label" for="test">Test</label>
  <div class="controls">
<select id="test" name="test" tabindex="2">
<option value=""> -- Select -- </option>
<option value="1">test 1</option>
<option value="2">test 2</option>
</select>
    
  </div>
</div>
  <div class="form-actions">
    <button tabindex="2" class="btn">Go</button>
  </div>
</form>
HTML;

		$this->assertSame($expected, $filter->render());

	}
}