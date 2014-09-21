<?php
namespace Former;

use Former\TestCases\FormerTests;
use Mockery;
use PHPUnit_Framework_Assert;
use ReflectionMethod;

class MethodDispatcherTest extends FormerTests
{
	public function testCanAddRepositories()
	{
		$dispatcher = new MethodDispatcher($this->app, array());
		$this->assertCount(0, PHPUnit_Framework_Assert::readAttribute($dispatcher, 'repositories'));

		$dispatcher->addRepository('A\Namespace\\');
		$this->assertCount(1, PHPUnit_Framework_Assert::readAttribute($dispatcher, 'repositories'));
		$this->assertContains('A\Namespace\\', PHPUnit_Framework_Assert::readAttribute($dispatcher, 'repositories'));
	}

	/**
	 * Test that camel-cased names like Former::fakeField get translated to
	 * a titlecased class of A\Fakefield.  This is the original Former approach.
	 */
	public function testSupportsTitleCasedFields()
	{
		$dispatcher = new MethodDispatcher($this->app, array('A\\'));
		$method     = new ReflectionMethod($dispatcher, 'getClassFromMethod');
		$method->setAccessible(true);

		$mock = Mockery::mock('A\Fakefield');

		$this->assertEquals('A\Fakefield', $method->invoke($dispatcher, 'fakefield'));
	}

	/**
	 * Test that camel-cased names (Former::fakeField) or snake-cased names
	 * (Fomer::fake_field) get translated to a study cased class name (FakeField)
	 */
	public function testSupportsCamelCasedAndSnakeCasedFields()
	{
		$dispatcher = new MethodDispatcher($this->app, array('A\\'));
		$method     = new ReflectionMethod($dispatcher, 'getClassFromMethod');
		$method->setAccessible(true);

		$mock = Mockery::mock('A\FakeField');

		$this->assertEquals('A\FakeField', $method->invoke($dispatcher, 'fakeField'));
		$this->assertEquals('A\FakeField', $method->invoke($dispatcher, 'fake_field'));
	}
}
