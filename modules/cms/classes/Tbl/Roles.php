<?php

defined('SYSPATH') or die('No direct script access.');

class Tbl_Roles extends Tbl {

	/**
	 * Construct
	 *
	 * @param string $name
	 */
	public function __construct()
	{
		$this->_columns = array(
			'id',
			'name',
			'description',
		);

		$this->_unchangeable = array(
			'id',
		);

		parent::__construct();
	}

	/**
	 * Validate
	 *
	 * @abstract Model_Table
	 * @param array $data
	 * @return Validation
	 * @throws Validation_Exception
	 */
	public function validate($data)
	{
		$validation = Validation::factory($data)
			// rule
			->rule('name', 'not_empty')
			->rule('name', 'max_length', array(':value', '32'))
			->rule('name', 'regex', array(':value', '/^[a-z0-9_]+$/'))
			->rule('name', array($this, 'uniquely'), array(':field', ':value'))
			->rule('description', 'max_length', array(':value', '255'))
			// Lavel
			->label('name', __('Name'))
			->label('description', __('Description'))
		;

		return $validation;
	}

}