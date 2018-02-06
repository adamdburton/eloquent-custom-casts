<?php

namespace AdamDBurton\EloquentCustomCasts\Test;

use AdamDBurton\EloquentCustomCasts\CustomCast;
use AdamDBurton\EloquentCustomCasts\HasCustomCasts;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Orchestra\Testbench\TestCase;

class TestCastAttribute extends CustomCast
{
	public function creating($value)
	{
		$this->data->value = $value;
	}
}

class TestModel extends Model
{
	use HasCustomCasts;

	protected $casts = [ 'test' => TestCastAttribute::class ];
}

class CustomCastTest extends TestCase
{
	private function getModel()
	{
		return new TestModel;
	}

	public function testModelInstantiates()
	{
		$this->assertInstanceOf(Model::class, $this->getModel());
	}

	public function testAttributeIsCast()
	{
		$this->assertInstanceOf(Carbon::class, $this->getModel()->created_at);
		$this->assertInstanceOf(TestCastAttribute::class, $this->getModel()->test);
	}
}