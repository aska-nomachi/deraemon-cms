<?php defined('SYSPATH') or die('No direct script access.');

class Validation_Exception extends Kohana_Validation_Exception {

	/*
	 * Additon error method [kohei]
	 */
	public function errors($file = NULL, $translate = TRUE)
	{
		// from Class Validatin errors method
		return $this->array->errors($file, $translate);
	}

}