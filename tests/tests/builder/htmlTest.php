<?php defined('SYSPATH') OR die('No direct script access.');

use PHPUnit\Framework\TestCase;

/**
 * Jamtart_Builder_HtmlTest 
 *
 * @group jam-tart
 * @group jam-tart.builder
 * @group jam-tart.builder.html
 * 
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
class Jamtart_Builder_HtmlTest extends TestCase {

	public function test_anchor()
	{
		$tag = new Builder_Html('div');

		$tag->anchor('/url/test', 'Text', array('class' => 'my-test-class'));
				
		$expected = <<<ANCHOR
<div>
  <a href="/url/test" class="my-test-class">Text</a>
</div>
ANCHOR;

		$this->assertSame($expected, $tag->render());
	}

	public function data_parse_css_name()
	{
		return array(
			array('div', NULL),
			array('div.small', array('tag' => 'div', 'attributes' => array('class' => 'small'))),
			array('span.small.hidden.pull-left', array('tag' => 'span', 'attributes' => array('class' => 'small hidden pull-left'))),
			array('div#first', array('tag' => 'div', 'attributes' => array('id' => 'first'))),
			array('span#first.small.hidden', array('tag' => 'span', 'attributes' => array('id' => 'first', 'class' => 'small hidden'))),
		);
	}
	
	/**
	 * @dataProvider data_parse_css_name
	 */
	public function test_parse_css_name($name, $expected)
	{
		$this->assertEquals($expected, Builder_Html::parse_css_name($name));
	}

	public function test_tag_with_css()
	{
		$tag = new Builder_Html('div.small.hidden', NULL, 'Test');
		$expected = <<<ANCHOR
<div class="small hidden">Test</div>
ANCHOR;
		$this->assertSame($expected, $tag->render());		
	}
	
	public function test_form()
	{
		$tag = new Builder_Html('div');
		$self = $this;
		$tag->form('/url/test', array('class' => 'my-test-class'), function($form) use ($self) {
			$self->assertInstanceOf('Builder_Form', $form);
			$form->add('Text');
		});
				
		$expected = <<<ANCHOR
<div>
  <form action="/url/test" method="POST" class="my-test-class" enctype="multipart/form-data">Text</form>
</div>
ANCHOR;
		$this->assertSame($expected, $tag->render());		
	}

}
