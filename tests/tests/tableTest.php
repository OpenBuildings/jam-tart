<?php defined('SYSPATH') OR die('No direct script access.');

use PHPUnit\Framework\TestCase;

/**
 * Jamtart_TableTest
 *
 * @group jam-tart
 * @group jam-tart.table
 *
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
class Jamtart_TableTest extends TestCase {

	/**
	 * Test Basic getters and setters
	 */
	public function test_getters_setters()
	{
		$collection = Jam::all('test_city');
		$columns = array('name' => new Tart_Column());
		$table = new Tart_Table($collection, $columns);

		$this->assertSame($collection, $table->collection());
		$this->assertEquals($columns, $table->columns());

		$this->assertEquals('name', Arr::get($table->columns(), 'name')->name());
		$this->assertEquals('Name', Arr::get($table->columns(), 'name')->label());

		$table->columns(array(
			'id' => new Tart_Column(),
			'size' => Tart::column()->name('population')->label('Big Size'),
		));

		$this->assertEquals('id', Arr::get($table->columns(), 'id')->name());
		$this->assertEquals('Id', Arr::get($table->columns(), 'id')->label());

		$this->assertEquals('population', Arr::get($table->columns(), 'size')->name());
		$this->assertEquals('Big Size', Arr::get($table->columns(), 'size')->label());

		$collection2 = Jam::all('test_country');
		$table->collection($collection2);
		$table->selected(array(10, 12));
		$this->assertSame($collection2, $table->collection());
		$this->assertSame(array(10, 12), $table->selected());

		$table->selected(FALSE);

		$this->assertSame(FALSE, $table->selected());
	}

	public function test_render()
	{
		$collection = Jam::all('test_city')->load_fields(array(
			array('id' => 1, 'name' => 'London', 'population' => 10),
			array('id' => 2, 'name' => 'New York', 'population' => 15),
		));
		$name = new Tart_Column();
		$name->sort(FALSE);
		$population = new Tart_Column();
		$population->sort(FALSE);
		$columns = array('name' => $name, 'population' => $population);
		$table = new Tart_Table($collection, $columns);
		$table->selected(FALSE);


		$expected = <<<HTML
<table class="table table-striped table-hover">
  <thead>
    <th>Name</th>
    <th>Population</th>
  </thead>
  <tbody>
    <tr class="test_city-1">
      <td>London</td>
      <td>10</td>
    </tr>
    <tr class="test_city-2">
      <td>New York</td>
      <td>15</td>
    </tr>
  </tbody>
</table>
HTML;
		$this->assertSame($expected, $table->render());

		$table->selected(array(1));

		$expected = <<<HTML_SELECTED
<table class="table table-striped table-hover">
  <thead>
    <th width="10">
      <input type="checkbox" name="all" value="1"/>
    </th>
    <th>Name</th>
    <th>Population</th>
  </thead>
  <tbody>
    <tr class="test_city-1">
      <td>
        <input type="checkbox" name="id[]" value="1" checked="1"/>
      </td>
      <td>London</td>
      <td>10</td>
    </tr>
    <tr class="test_city-2">
      <td>
        <input type="checkbox" name="id[]" value="2"/>
      </td>
      <td>New York</td>
      <td>15</td>
    </tr>
  </tbody>
</table>
HTML_SELECTED;

		$this->assertSame($expected, $table->render());
	}
}
