<?php

defined('SYSPATH') or die('No direct script access.');

class Tbl_Email_Rules extends Tbl {

	/**
	 * Construct
	 *
	 * @param string $name
	 */
	public function __construct()
	{
		$this->_columns = array(
			'id',
			'email_id',
			'field',
			'callback',
			'param',
			'label',
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
			->rule('email_id', 'not_empty')
			->rule('field', 'not_empty')
			->rule('callback', 'not_empty')
			// Lavel
			->label('email_id', __('Email ID'))
			->label('field', __('Field'))
			->label('callback', __('Callback'))
		;

		return $validation;
	}

}