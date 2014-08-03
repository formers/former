<?php
namespace Former\Dummy;

class DummyToArray
{
	protected $values;

	public function __construct(array $values = array())
	{
		$this->values = $values;
	}

	public function toArray()
	{
		return $this->values;
	}
}
