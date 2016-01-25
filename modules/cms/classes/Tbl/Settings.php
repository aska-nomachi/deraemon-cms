<?php

defined('SYSPATH') or die('No direct script access.');

class Tbl_Settings extends Tbl {

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
			'key',
			'value',
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
			->rule('name', 'max_length', array(':value', '200'))
			->rule('key', 'not_empty')
			->rule('key', 'max_length', array(':value', '200'))
			->rule('key', 'regex', array(':value', '/^[a-z0-9_]+$/'))
			->rule('key', array($this, 'uniquely'), array(':field', ':value'))
			// Lavel
			->label('name', __('Name'))
			->label('key', __('Key'))
		;

		return $validation;
	}

}