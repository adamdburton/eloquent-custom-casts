<?php

namespace AdamDBurton\EloquentCustomCasts;

use Illuminate\Database\Eloquent\Model;

trait HasCustomCasts
{
	public static function bootHasCustomCasts()
	{
		static::retrieved(function(Model $model)
		{
			foreach($model->getCasts() as $field => $class)
			{
				if(class_exists($class))
				{
					$model->addCustomCast($field, $class);
				}
			}
		});

		static::saving(function(Model $model)
		{
			$dirty = $model->getDirty();

			foreach($model->getCustomCasts() as $key => $class)
			{
				$model->setAttribute($key, serialize($dirty[$key]));
			}
		});
	}

	public function addCustomCast($key, $class)
	{
		if(!isset($this->customCasts))
		{
			$this->customCasts = [];
		}

		$this->customCasts[$key] = $class;

		return $this;
	}

	public function removeCustomCast($key)
	{
		if(!isset($this->customCasts))
		{
			$this->customCasts = [];
		}

		unset($this->customCasts[$key]);
	}

	public function getCustomCasts()
	{
		return $this->customCasts ?? [];
	}

	public function getAttributeValue($key)
	{
		if(isset($this->customCasts[$key]))
		{
			$value = $this->attributes[$key];
			$class = $this->customCasts[$key];

			return $value ? unserialize($this->attributes[$key]) : new $class;
		}

		return parent::getAttributeValue($key);
	}
}