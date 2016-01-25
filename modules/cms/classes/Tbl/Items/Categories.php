<?php

defined('SYSPATH') or die('No direct script access.');

class Tbl_Items_Categories extends Tbl {

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
			'category_id',
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
			->rule('category_id', 'not_empty')
			->rule('category_id', 'numeric')
			// Lavel
			->label('item_id', __('Item ID'))
			->label('category_id', __('Dategory ID'))
		;

		return $validation;
	}

}