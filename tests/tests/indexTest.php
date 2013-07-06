<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Jamtart_IndexTest 
 *
 * @group jam-tart
 * @group jam-tart.index
 * 
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
class Jamtart_IndexTest extends PHPUnit_Framework_TestCase {

	public function test_getters_setters()
	{
		$collection = Jam::all('test_city');
		$columns = array('name' => new Tart_Column());
		$index = new Tart_Index($collection, 0, $columns);
		$index->batch_actions(array('test' => 'batch'));

		$this->assertSame($collection, $index->collection());
		$this->assertEquals($columns, $index->columns());
		$this->assertEquals(array('test' => 'batch'), $index->batch_actions());
	}

	public function test_render_batch_actions()
	{
		$collection = Jam::all('test_city');
		$index = new Tart_Index($collection, 0);
		$index->batch_actions(array('test' => 'batch'));

		$expected = <<<HTML
<div class="form-inline">
  <label>With Selected:</label>
  <button type="submit" name="action" value="test" class="btn">batch</button>
</div>
HTML;

		$this->assertEquals($expected, (string) $index->render_batch_actions());
	}
}