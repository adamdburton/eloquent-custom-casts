<?php

namespace AdamDBurton\EloquentCustomCasts;

use Illuminate\Database\Eloquent\Model;

trait HasCustomCasts
{
	public static function bootHasCustomCasts()
	{
		static::retrieved(function(Model $model)
		{
			$model->castCustomAttributes();
		});

		static::saving(function(Model $model)
		{
			$original = $model->getOriginal('image');
			$attribute = $model->getAttribute('image');
			$same = $original === $attribute;

			var_dump($original);
			var_dump($attribute);
			die('Image attributes are ' . ($same ? '' : 'not ') . 'the same!');

		});
	}

	public function castCustomAttributes()
	{
		$casts = $this->getCasts();

		foreach($casts as $key => $class)
		{
			if(class_exists($class))
			{
				$value = $this->attributes[$key];

				$instance = new $class;

				if($value)
				{
					$value = json_decode($value);
					$instance->restoring($value);
				}

				$this->original[$key] = $instance;
				$this->attributes[$key] = clone($instance);
			}
		}
	}
}