<?php
namespace Former;

use Former\TestCases\FormerTests;

class FormTest extends FormerTests
{
	////////////////////////////////////////////////////////////////////
	////////////////////////////// MATCHERS ////////////////////////////
	////////////////////////////////////////////////////////////////////

	public function matchForm($class = 'horizontal', $files = false, $action = '#')
	{
		if (in_array($class, array('horizontal', 'inline', 'vertical', 'search'))) {
			$class = 'form-'.$class;
		}

		$matcher = array(
			'tag'        => 'form',
			'attributes' => array(
				'class'          => $class,
				'method'         => 'POST',
				'accept-charset' => 'utf-8',
				'action'         => $action,
			),
		);

		if ($files) {
			$matcher['attributes']['enctype'] = 'multipart/form-data';
		}

		return $matcher;
	}

	public function createMatcher($class = 'horizontal', $forFiles = false, $action = '#')
	{
		if (in_array($class, array('horizontal', 'inline', 'vertical', 'search'))) {
			$class = 'form-'.$class;
		}
		$forFiles = $forFiles ? 'enctype="multipart/form-data" ' : null;
		$action   = $action ? 'action="'.$action.'" ' : null;

		return '<form '.$forFiles.'class="'.$class.'" method="POST" '.$action.'accept-charset="UTF-8">';
	}

	////////////////////////////////////////////////////////////////////
	//////////////////////////////// TESTS /////////////////////////////
	////////////////////////////////////////////////////////////////////

	public function testCanOpenAClassicForm()
	{
		$open    = $this->former->open('#')->__toString();
		$matcher = $this->matchForm();

		$this->assertHTML($matcher, $open);
	}

	public function testCanOpenAFormWithoutAction()
	{
		$open    = $this->former->open()->__toString();
		$matcher = $this->matchForm('horizontal', false, false);

		$this->assertHTML($matcher, $open);
	}

	public function testCanCloseAForm()
	{
		$close = $this->former->close();

		$this->assertEquals('<input type="hidden" name="_token" value="csrf_token"></form>', $close);
	}

	public function testDoesntAddTokenToGetForms()
	{
		$open  = $this->former->open()->method('GET');
		$close = $this->former->close();

		$this->assertEquals('</form>', $close);
	}

	public function testCanCreateCustomFormOpener()
	{
		$open                              = $this->former->open('#', 'GET', $this->testAttributes)->__toString();
		$matcher                           = $this->matchForm('foo form-horizontal');
		$matcher['attributes']['method']   = 'GET';
		$matcher['attributes']['data-foo'] = 'bar';

		$this->assertHTML($matcher, $open);
	}

	public function testCanCreateHorizontalForm()
	{
		$open    = $this->former->horizontal_open('#')->__toString();
		$matcher = $this->matchForm();

		$this->assertHTML($matcher, $open);
	}

	public function testCanCreateVerticalForm()
	{
		$open    = $this->former->vertical_open('#')->__toString();
		$matcher = $this->matchForm('vertical');

		$this->assertHTML($matcher, $open);
	}

	public function testCanCreateSearchForm()
	{
		$open    = $this->former->search_open('#')->__toString();
		$matcher = $this->matchForm('search');

		$this->assertHTML($matcher, $open);
	}

	public function testCanCreateInlineForm()
	{
		$open    = $this->former->inline_open('#')->__toString();
		$matcher = $this->matchForm('inline');

		$this->assertHTML($matcher, $open);
	}

	public function testCanCreateFilesForm()
	{
		$open    = $this->former->open_for_files('#')->__toString();
		$matcher = $this->matchForm('horizontal', true);

		$this->assertHTML($matcher, $open);
	}

	// Combining features

	public function testCanCreateAnInlineFilesForm()
	{
		$open    = $this->former->inline_open_for_files('#')->__toString();
		$matcher = $this->matchForm('inline', true);

		$this->assertHTML($matcher, $open);
	}

	public function testCanCreateAnInlineSecureFilesForm()
	{
		$open    = $this->former->inline_secure_open_for_files('#')->__toString();
		$matcher = $this->matchForm('inline', true);

		$this->assertHTML($matcher, $open);
	}

	public function testCanChainMethods()
	{
		$open1 = $this->former->open('test')->secure()->addClass('foo')->method('GET')->__toString();
		$open2 = $this->former->horizontal_open('#')->class('form-vertical bar')->__toString();

		$matcher1                         = $this->matchForm('form-horizontal foo', false, 'https://test/en/test');
		$matcher1['attributes']['method'] = 'GET';
		$matcher2                         = $this->matchForm('form-vertical bar');

		$this->assertHTML($matcher1, $open1);
		$this->assertHTML($matcher2, $open2);
	}

	public function testCanDirectlyAddRulesToAForm()
	{
		// Check form opener
		$open    = $this->former->open('#')->rules(array('foo' => 'required'))->addClass('foo')->__toString();
		$matcher = $this->matchForm('form-horizontal foo');
		$this->assertHTML($matcher, $open);

		// Check field
		$input = $this->former->text('foo')->__toString();
		$label = $this->matchLabel('Foo', 'foo', true);

		$this->assertHTML($this->matchField(array('required' => null)), $input);
		$this->assertHTML($label, $input);
	}

	public function testCanChainAttributes()
	{
		$open                            = $this->former->open()->method('GET')->id('form')->action('#')->addClass('foo')->__toString();
		$matcher                         = $this->matchForm('form-horizontal foo');
		$matcher['id']                   = 'form';
		$matcher['attributes']['method'] = 'GET';

		$this->assertHTML($matcher, $open);
	}

	public function testInlineFormsAreRecognized()
	{
		$open  = $this->former->inline_open()->render();
		$field = $this->former->text('foo');

		$this->assertHTML($this->matchField(), $field->__toString());
	}

	public function testCanSetNameOnFormOpeners()
	{
        $this->markTestSkipped('Fatals with `Could not load XML from object`');

		$form                          = $this->former->open('#')->name('foo');
		$matcher                       = $this->matchForm();
		$matcher['attributes']['name'] = 'foo';

		$this->assertHTML($matcher, $form);
	}

	public function testDifferentFormsCanPopulateDifferentValues()
	{
		$this->former->populate(array('foo' => 'eatshitanddie'));

		$this->former->open('#')->populate(array('foo' => 'bar'));
		$field = $this->former->text('foo');
		$this->former->close();

		$this->former->open('#')->populate(array('foo' => 'foo'));
		$fieldTwo = $this->former->text('foo');
		$this->former->close();

		$this->assertEquals('bar', $field->getValue());
		$this->assertEquals('foo', $fieldTwo->getValue());
	}

	public function testPopulateReturnsFormOpener()
	{
        $this->markTestSkipped('Fatals with `Could not load XML from object`');

		$form = $this->former->open('#')->populate(array());

		$this->assertHTML($this->matchForm(), $form);
	}

	public function provideMethods()
	{
		return array(
			array('PUT'),
			array('PATCH'),
			array('DELETE'),
		);
	}

	/**
	 * @dataProvider provideMethods
	 */
	public function testCanOpenToSpoofedMethod($method)
	{
		$form    = $this->former->open('#')->method($method)->__toString();
		$matcher = $this->matchForm();

		$this->assertHTML($matcher, $form);
		$this->assertHTML(array(
			'tag'        => 'input',
			'attributes' => array(
				'type'  => 'hidden',
				'name'  => '_method',
				'value' => $method,
			),
		), $form);
	}

	public function testCanOpenAFormToRoute()
	{
        $this->markTestSkipped('Fatals with `Could not load XML from object`');

		$form       = $this->former->open()->route('user.edit', array(2));
		$formSingle = $this->former->open()->route('user.edit', 2);

		$matcher = $this->matchForm('horizontal', false, '/users/2/edit');
		$this->assertHTML($matcher, $form);
		$this->assertHTML($matcher, $formSingle);
	}

	public function testCanOpenFormToAControllerMethod()
	{
        $this->markTestSkipped('Fatals with `Could not load XML from object`');

		$form       = $this->former->open()->controller('UsersController@edit', array(2));
		$formSingle = $this->former->open()->controller('UsersController@edit', 2);

		$matcher = $this->matchForm('horizontal', false, '/users/2/edit');
		$this->assertHTML($matcher, $form);
		$this->assertHTML($matcher, $formSingle);
	}

	public function testFormCanHaveDifferentFrameworkFromMain()
	{
		$form = $this->former->raw_open();

		$this->assertEquals('TwitterBootstrap', $this->app['former.framework']->current());
		$this->assertEquals('Nude', $this->app['former.form.framework']->current());

		$this->former->framework('Nude');
		$this->assertEquals('Nude', $this->app['former.framework']->current());
		$this->assertEquals('Nude', $this->app['former.form.framework']->current());

		$this->former->framework('TwitterBootstrap');
		$this->assertEquals('TwitterBootstrap', $this->app['former.framework']->current());
		$this->assertEquals('Nude', $this->app['former.form.framework']->current());
	}

	public function testCanOpenRawForm()
	{
		$this->former->framework('TwitterBootstrap3');

		$form = $this->former->raw_open();
		$form .= $this->former->text('foo');
		$form .= $this->former->actions()->large_submit('Submit');
		$form .= $this->former->close();

		$matcher =
			'<form accept-charset="utf-8" method="POST">'.
			'<input id="foo" type="text" name="foo">'.
			'<div><input class="large" type="submit" value="Submit"></div>'.
			'<input type="hidden" name="_token" value="csrf_token">'.
			'</form>';

		$this->assertEquals($matcher, $form);
	}

	public function testCanOpenFormWithCamelCase()
	{
		$open    = $this->former->verticalOpen('#')->__toString();
		$matcher = $this->matchForm('vertical');

		$this->assertHTML($matcher, $open);
	}

	public function testClosingFormRemovesFrameworkInstance()
	{
		$form = $this->former->raw_open();
		$this->assertEquals('TwitterBootstrap', $this->app['former.framework']->current());
		$this->assertEquals('Nude', $this->app['former.form.framework']->current());
		$this->former->close();

		$this->assertFalse($this->app->bound('former.form.framework'));
	}
}
