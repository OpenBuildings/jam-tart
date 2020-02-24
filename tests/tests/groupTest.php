<?php defined('SYSPATH') OR die('No direct script access.');

use PHPUnit\Framework\TestCase;

/**
 * Jamtart_GroupTest 
 *
 * @group jam-tart
 * @group jam-tart.group
 * 
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
class Jamtart_GroupTest extends TestCase {

	public function test_getters_setters()
	{
		$items = array('name_param' => new Tart_Column(), 'name2' => new Tart_Column());

		$group = new Tart_Table();
		$group->items($items);

		$this->assertSame($items['name_param'], $group->items('name_param'));
		$this->assertEquals('name_param', $group->items('name_param')->name());
		$this->assertEquals('Name param', $group->items('name_param')->label());
		
		$this->assertSame($items['name2'], $group->items('name2'));
		$this->assertEquals('name2', $group->items('name2')->name());
		$this->assertEquals('Name2', $group->items('name2')->label());
	}
}
