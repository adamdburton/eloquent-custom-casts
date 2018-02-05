<?php

namespace AdamDBurton\EloquentCustomCasts;

use Illuminate\Database\Eloquent\Model;

trait HasCustomCasts
{
	public function getAttribute($key)
	{
		$casts = $this->getCasts();

		if(isset($casts[$key]) && class_exists($casts[$key]))
		{
			$class = $casts[$key];
			$value = $this->attributes[$key];

			if($value)
			{
				if(is_object($value) && is_subclass_of($value, $class))
				{
					return $value; // Already the right class, let it through
				}

				$json = @json_decode($value);

				if(json_last_error() == JSON_ERROR_NONE)
				{
					$obj = new $class;
					$obj->restoring($json);

					return $obj; // Encoded version of the class
				}
				else
				{
					$obj = new $class;
					$obj->creating($value);

					return $obj; // We got some other value, pass it to a new instance of the class
				}
			}
			else
			{
				return new $class; // No value, pass it as a new instance of the class
			}
		}

		// Pass back to the parent to deal with!

		return parent::getAttribute($key);
	}

	public function setAttribute($key, $value)
	{
		$casts = $this->getCasts();

		if(isset($casts[$key]) && class_exists($casts[$key]))
		{
			// Check if the value being set as this attribute is already the class we expect
			// and if so, replace it, otherwise, pass the value to a new instance of it

			$class = $casts[$key];

			if(is_subclass_of($value, $class))
			{
				$this->attributes[$key] = $value;

				return $this;
			}
			else
			{
				$obj = new $class();
				$obj->creating($value);

				$this->attributes[$key] = $obj;

				return $this;
			}
		}

		return parent::setAttribute($key, $value);
	}
}