<?php

namespace AdamDBurton\EloquentCustomCasts;

abstract class CustomCast implements Castable
{
	protected $data;

	public function __construct($value = null)
	{
		if($value)
		{
			$this->creating($value);
		}
	}

	public function __toString()
	{
		// This method returns the data that will be saved to the database.
		// If the json returned differs from what is in the database, it's marked as dirty

		return json_encode($this->saving());
	}

	public function restoring($data)
	{
		$this->data = $data;
	}

	public function saving()
	{
		return $this->data;
	}
}