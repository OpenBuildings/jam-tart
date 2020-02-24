<?php defined('SYSPATH') OR die('No direct script access.');

use PHPUnit\Framework\TestCase;

/**
 * Jamtart_Index_ActionsTest 
 *
 * @group jam-tart
 * @group jam-tart.column
 * @group jam-tart.column.actions
 * 
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
class Jamtart_Column_ActionsTest extends TestCase {

	use Trait_DomSearch;

	public function test_actions()
	{
		$city = Jam::build('test_city')->load_fields(array('id' => 1, 'name' => 'First Name', 'population' => 300));

		$actions = new Tart_Column_Actions(function($item){
			return 
				HTML::anchor(Tart::uri($item), 'Edit')
				.'<span> info </span>';
		});

		$rendered = $actions->item($city)->render();

		$this->assertSelectEquals('a[href="'.Tart::uri($city).'"]', 'Edit', TRUE, $rendered);
		$this->assertSelectEquals('span', 'info', TRUE, $rendered);

	}

}
