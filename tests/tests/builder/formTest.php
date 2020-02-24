<?php defined('SYSPATH') OR die('No direct script access.');

use PHPUnit\Framework\TestCase;

/**
 * Jamtart_Builder_FormTest 
 *
 * @group jam-tart
 * @group jam-tart.builder
 * @group jam-tart.builder.form
 * 
 * @package Jam tart
 * @author Ivan Kerin
 * @copyright  (c) 2011-2013 Despark Ltd.
 */
class Jamtart_Builder_FormTest extends TestCase {
  
  public function test_inputs()
  {
    $tag = new Builder_Html('div');
    $tag->form('/url/test', function($form){
      $form->input('name1');
      $form->input('name1', 'value1');
      $form->input('name1', 'value1', array('type' => 'email'));

      $form->hidden('name2', 'value2');

      $form->password('name2', array('class' => 'my-class'));

      $form->file('name3', array('class' => 'my-class'));

      $form->checkbox('name4', 'value4', TRUE, array('class' => 'my-class'));
      $form->checkbox('name4', 'value5', FALSE, array('class' => 'my-class'));

      $form->radio('name4', 'value4', TRUE, array('class' => 'my-class'));
      $form->radio('name4', 'value5', FALSE, array('class' => 'my-class'));

      $form->select('name5', array('choice1' => 'value1', 'choice2' => 'value2'), 'choice1', array('class' => 'my-class'));
      $form->select('name5', array('choice1' => 'value1', 'choice2' => 'value2'), array('choice1', 'choice2'), array('class' => 'my-class', 'multiple'));
      $form->select('name5', array('choice1' => 'value1', 'choice2' => 'value2'), NULL, array('class' => 'my-class', 'multiple'));
      $form->select('name5', array('choice1' => 'value1', 'choice2' => array('subchoice1' => 'subvalue1', 'subchoice2' => 'subvalue2')), 'subchoice2', array('class' => 'my-class'));

    });

    $expected = <<<ANCHOR
<div>
  <form action="/url/test" method="POST" enctype="multipart/form-data">
    <input type="text" name="name1"/>
    <input type="text" name="name1" value="value1"/>
    <input type="email" name="name1" value="value1"/>
    <input type="hidden" name="name2" value="value2"/>
    <input type="password" name="name2" class="my-class"/>
    <input type="file" name="name3" class="my-class"/>
    <input type="checkbox" name="name4" value="value4" class="my-class" checked="checked"/>
    <input type="checkbox" name="name4" value="value5" class="my-class"/>
    <input type="radio" name="name4" value="value4" class="my-class" checked="checked"/>
    <input type="radio" name="name4" value="value5" class="my-class"/>
    <select name="name5" class="my-class">
      <option value="choice1" selected="selected">value1</option>
      <option value="choice2">value2</option>
    </select>
    <select name="name5" class="my-class" multiple="multiple">
      <option value="choice1" selected="selected">value1</option>
      <option value="choice2" selected="selected">value2</option>
    </select>
    <select name="name5" class="my-class" multiple="multiple">
      <option value="choice1">value1</option>
      <option value="choice2">value2</option>
    </select>
    <select name="name5" class="my-class">
      <option value="choice1">value1</option>
      <optgroup>
        <option value="subchoice1">subvalue1</option>
        <option value="subchoice2" selected="selected">subvalue2</option>
      </optgroup>
    </select>
  </form>
</div>
ANCHOR;
    $this->assertSame($expected, $tag->render());   
  }
}
