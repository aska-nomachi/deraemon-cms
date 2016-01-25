<?php

defined('SYSPATH') or die('No direct script access.');

class Tbl_Items_Fields extends Tbl {

	/**
	 * Construct
	 *
	 * @param string $name
	 */
	public function __construct()
	{
		$this->_columns = array(
			'id',
			'item_id',
			'field_id',
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
			->rule('item_id', 'not_empty')
			->rule('item_id', 'numeric')
			->rule('field_id', 'not_empty')
			->rule('field_id', 'numeric')
			// Lavel
			->label('item_id', __('Item ID'))
			->label('field_id', __('Field ID'))
		;

		return $validation;
	}

}