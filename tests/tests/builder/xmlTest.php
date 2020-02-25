<?php defined('SYSPATH') OR die('No direct script access.');

use PHPUnit\Framework\TestCase;

/**
 * Jamtart_Builder_XmlTest 
 *
 * @group jam-tart
 * @group jam-tart.builder
 * @group jam-tart.builder.xml
 * 
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
class Jamtart_Builder_XmlTest extends TestCase {
	
	public function data_arguments()
	{
		$closure = function(){ };
		return array(
			array(
				array('tag1'), 
				array('name' => 'tag1', 'content' => NULL, 'attributes' => NULL)
			),
			array(
				array('tag2', array('class' => 'my-class')), 
				array('name' => 'tag2', 'content' => NULL, 'attributes' => array('class' => 'my-class'))
			),
			array(
				array('tag3', 'text content'), 
				array('name' => 'tag3', 'content' => 'text content', 'attributes' => NULL)
			),
			array(
				array('tag4', array('class' => 'my-class'), $closure), 
				array('name' => 'tag4', 'content' => $closure, 'attributes' => array('class' => 'my-class'))
			),
			array(
				array('tag5', 'text content', array('class' => 'my-class')), 
				array('name' => 'tag5', 'content' => 'text content', 'attributes' => array('class' => 'my-class'))
			),
			array(
				array('tag6', 'text content', $closure), 
				array('name' => 'tag6', 'content' => $closure, 'attributes' => NULL)
			),
		);
	}

	/**
	 * @dataProvider data_arguments
	 */
	public function test_arguments($arguments, $expected)
	{
		$this->assertEquals($expected, Builder_Xml::arguments($arguments));
	}

	public function test_no_name()
	{
		$tag = new Builder_Xml(NULL, NULL, 'only content');

		$this->assertEquals('only content', $tag->render());
	}

	public function test_add()
	{
		$tag = new Builder_Xml('tag1');
		$child = new Builder_Xml('tag2');

		$tag->add('Some Text');
		$this->assertEquals('Some Text', Arr::get($tag->children(), 0));
		
		$tag->add($child);
		$this->assertSame($child, Arr::get($tag->children(), 1));
	}

	public function test_context()
	{
		$test = $this;
		$tag = new Builder_Xml('tag1', NULL, function($h, $self) use ($test) {
			$test->assertSame($test, $self);
			$test->assertInstanceOf('Builder_Xml', $h);

			$h('tag2', function($h, $self) use ($test) {
				$test->assertSame($test, $self);
				$test->assertInstanceOf('Builder_Xml', $h);
			});
		}, $this);

		$tag->render();
	}

	public function test_has_content()
	{
		$tag = new Builder_Xml('tag1');
		$child = new Builder_Xml('tag2');

		$this->assertFalse($tag->has_content());
		$tag->add($child);

		$this->assertTrue($tag->has_content());

		$tag = new Builder_Xml('tag1', array(), 'asdasd');
		$this->assertTrue($tag->has_content());

		$tag = new Builder_Xml('tag1', array(), function(){ });
		$this->assertTrue($tag->has_content());
	}

	public function test_tag()
	{
		$tag = new Builder_Xml('tag1');
		$closure = function(){};

		$tag->tag('tag2');
		$tag->tag('tag3', array('class' => 'my-class'));
		$tag->tag('tag4', array('class' => 'my-class-2'), $closure);
		$tag->tag('tag5', array('class' => 'my-class-3'), 'content');
		$children = $tag->children();

		$this->assertEquals('tag2', $children[0]->name());

		$this->assertEquals('tag3', $children[1]->name());
		$this->assertEquals(array('class' => 'my-class'), $children[1]->attributes());

		$this->assertEquals('tag4', $children[2]->name());
		$this->assertEquals(array('class' => 'my-class-2'), $children[2]->attributes());
		$this->assertEquals($closure, $children[2]->content());

		$this->assertEquals('tag5', $children[3]->name());
		$this->assertEquals(array('class' => 'my-class-3'), $children[3]->attributes());
		$this->assertEquals('content', $children[3]->content());
	}

	public function test_invoke()
	{
		$tag = new Builder_Xml('tag1');
		$closure = function(){};

		$tag('tag2');
		$tag('tag3', array('class' => 'my-class'));
		$tag('tag4', array('class' => 'my-class-2'), $closure);
		$tag('tag5', array('class' => 'my-class-3'), 'content');
		$children = $tag->children();

		$this->assertEquals('tag2', $children[0]->name());

		$this->assertEquals('tag3', $children[1]->name());
		$this->assertEquals(array('class' => 'my-class'), $children[1]->attributes());

		$this->assertEquals('tag4', $children[2]->name());
		$this->assertEquals(array('class' => 'my-class-2'), $children[2]->attributes());
		$this->assertEquals($closure, $children[2]->content());

		$this->assertEquals('tag5', $children[3]->name());
		$this->assertEquals(array('class' => 'my-class-3'), $children[3]->attributes());
		$this->assertEquals('content', $children[3]->content());
	}

	public function test_render()
	{
		$tag = new Builder_Xml('tag1');

		$tag('tag2');
		$tag('tag3', array('class' => 'my-class'));
		$tag('tag4', array('class' => 'my-class-2'), function($tag){
			$tag('tag5', array('class' => 'my-class-3'), function($tag){
				$tag('tag6', array('class' => 'my-class-4'), 'content-tag6');
				$tag('tag7', array('class' => 'my-class-5'));
				$tag->add('some text');
			});
		});
		$tag('tag8', array('class' => 'my-class-6'), 'content');
		
		$expected = <<<HTML
<tag1>
  <tag2/>
  <tag3 class="my-class"/>
  <tag4 class="my-class-2">
    <tag5 class="my-class-3">
      <tag6 class="my-class-4">content-tag6</tag6>
      <tag7 class="my-class-5"/>
      some text
    </tag5>
  </tag4>
  <tag8 class="my-class-6">content</tag8>
</tag1>
HTML;
		$this->assertSame($expected, $tag->render());
	}
}
