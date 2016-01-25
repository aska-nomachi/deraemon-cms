<?php

defined('SYSPATH') or die('No direct script access.');

class Tbl_Roles_Users extends Tbl {

	/**
	 * Construct
	 *
	 * @param string $name
	 */
	public function __construct()
	{
		$this->_columns = array(
			'id',
			'role_id',
			'user_id',
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
			->rule('role_id', 'not_empty')
			->rule('role_id', 'numeric')
			->rule('user_id', 'not_empty')
			->rule('user_id', 'numeric')
			// Lavel
			->label('role_id', __('Role ID'))
			->label('user_id', __('User ID'))
		;

		return $validation;
	}

}