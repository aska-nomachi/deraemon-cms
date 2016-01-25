<?php

defined('SYSPATH') or die('No direct script access.');

class Tbl_Detail_Rules extends Tbl {

	/**
	 * Construct
	 *
	 * @param string $name
	 */
	public function __construct()
	{
		$this->_columns = array(
			'id',
			'detail_id',
			'callback',
			'param',
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
			->rule('detail_id', 'not_empty')
			->rule('callback', 'not_empty')
			// Lavel
			->label('detail_id', __('Detail ID'))
			->label('callback', __('Callback'))
		;

		return $validation;
	}

}