<?php
namespace Former;

use Former\MethodDispatcher;
use Former\TestCases\FormerTests;
use PHPUnit_Framework_Assert;

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
}
