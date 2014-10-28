<?php
namespace Former\Dummy;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Mockery;

class DummyEloquent extends Model
{
	/**
	 * The guarded attributes
	 *
	 * @var array
	 */
	protected $guarded = array();

	public function roles()
	{
		return Mockery::mock('Illuminate\Database\Eloquent\Relations\HasMany')
		              ->shouldReceive('getResults')->andReturn(array(
				new DummyEloquent(array('id' => 1, 'name' => 'foo')),
				new DummyEloquent(array('id' => 3, 'name' => 'bar')),
			))
		              ->mock();
	}

	public function rolesAsCollection()
	{
		return Mockery::mock('Illuminate\Database\Eloquent\Relations\HasMany')
			->shouldReceive('getResults')->andReturn(new Collection(array(
				new DummyEloquent(array('id' => 1, 'name' => 'foo')),
				new DummyEloquent(array('id' => 3, 'name' => 'bar')),
			)))
			->mock();
	}

	public function getCustomAttribute()
	{
		return 'custom';
	}

	public function __toString()
	{
		return $this->name;
	}
}
