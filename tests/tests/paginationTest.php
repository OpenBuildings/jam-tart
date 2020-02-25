<?php defined('SYSPATH') OR die('No direct script access.');

use PHPUnit\Framework\TestCase;

/**
 * Jamtart_PaginationTest 
 *
 * @group jam-tart
 * @group jam-tart.pagination
 * 
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
class Jamtart_PaginationTest extends TestCase {

	public function test_getters_setters()
	{
		$pagination = new Tart_Pagination(1000, 100);

		$this->assertEquals(1000, $pagination->total());
		$this->assertEquals(100, $pagination->offset());
		$this->assertEquals(50, $pagination->per_page());
		$this->assertEquals(NULL, $pagination->controller());

		$pagination->total(400);
		$pagination->offset(200);
		$pagination->per_page(100);
		$pagination->controller('test_cities');

		$this->assertEquals(400, $pagination->total());
		$this->assertEquals(200, $pagination->offset());
		$this->assertEquals(100, $pagination->per_page());
		$this->assertEquals('test_cities', $pagination->controller());
	}

	public function test_apply()
	{
		$collection = $this
			->getMockBuilder(Jam_Query_Builder_Collection::class)
			->disableOriginalConstructor()
			->getMock();

		$collection
			->expects($this->once())
			->method('limit')
			->with(300)
			->will($this->returnValue($collection));

		$collection
			->expects($this->once())
			->method('offset')
			->with(100)
			->will($this->returnValue($collection));

		$pagination = new Tart_Pagination(1000, 100);
		$pagination->per_page(300);

		$pagination->apply($collection);
	}

	public function test_next()
	{
		$pagination = new Tart_Pagination(1000, 100);
		$pagination->per_page(100);
		$pagination->controller('test_cities');

		$controller_url = Tart::uri('test_cities');

		$this->assertEquals("<a href=\"{$controller_url}?offset=200\">Next &raquo;</a>", (string) $pagination->next());
		$this->assertEquals("<a href=\"{$controller_url}?offset=900\">Next &raquo;</a>", (string) $pagination->offset(800)->next());
		$this->assertEquals('<span>Next &raquo;</span>', (string) $pagination->offset(950)->next());
	}

	public function test_previous()
	{
		$pagination = new Tart_Pagination(1000, 100);
		$pagination->per_page(100);
		$pagination->controller('test_cities');

		$controller_url = Tart::uri('test_cities');

		$this->assertEquals("<a href=\"{$controller_url}?offset=0\">&laquo; Previous</a>", (string) $pagination->previous());
		$this->assertEquals("<a href=\"{$controller_url}?offset=150\">&laquo; Previous</a>", (string) $pagination->offset(250)->previous());
		$this->assertEquals('<span>&laquo; Previous</span>', (string) $pagination->offset(0)->previous());
	}


	public function test_render()
	{
		$pagination = new Tart_Pagination(1000, 100);
		$pagination->per_page(100);
		$pagination->controller('test_cities');

		$controller_url = Tart::uri('test_cities');

		$expected = <<<HTML
<form action="{$controller_url}" method="GET" class="form-inline" enctype="multipart/form-data">
  <ul class="pager">
    <li class="previous">
<a href="{$controller_url}?offset=0">&laquo; Previous</a>
    </li>
    <li class="next">
<a href="{$controller_url}?offset=200">Next &raquo;</a>
    </li>
    <li class="pagination-control">
      <label>
        Showing: 100 - 200 of 1000
      </label>
      <span style="display:none">
        <input type="range" id="pagination-slider" value="100" class="input-large" min="0" step="100" max="1000"/>
        <input type="number" id="pagination-input" name="offset" value="100" class="input-mini" min="0" step="100" max="1000"/>
        <button type="submit" class="btn">Go</button>
      </span>
    </li>
  </ul>
</form>
HTML;
		$this->assertSame($expected, (string) $pagination->render());
	}
}
