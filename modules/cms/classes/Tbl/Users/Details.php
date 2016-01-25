<?php

defined('SYSPATH') or die('No direct script access.');

class Tbl_Users_Details extends Tbl {

	/**
	 * Construct
	 *
	 * @param string $name
	 */
	public function __construct()
	{
		$this->_columns = array(
			'id',
			'user_id',
			'detail_id',
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
			->rule('user_id', 'not_empty')
			->rule('user_id', 'numeric')
			->rule('detail_id', 'not_empty')
			->rule('detail_id', 'numeric')
			// Lavel
			->label('user_id', __('User ID'))
			->label('detail_id', __('Detail ID'))
		;

		return $validation;
	}

}