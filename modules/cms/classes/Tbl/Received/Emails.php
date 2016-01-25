<?php

defined('SYSPATH') or die('No direct script access.');

class Tbl_Received_Emails extends Tbl {

	/**
	 * Construct
	 *
	 * @param string $name
	 */
	public function __construct()
	{
		$this->_columns = array(
			'id',
			'email_segment',
			'json',
			'created',
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
			->rule('email_segment', 'not_empty')
			->rule('json', 'not_empty')
			// Lavel
			->label('email_segment', __('Email Segment'))
			->label('json', __('Json'))
		;

		return $validation;
	}

}