<?php

defined('SYSPATH') or die('No direct script access.');

class Tbl_Items_Tags extends Tbl {

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
			'tag_id',
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
			->rule('tag_id', 'not_empty')
			->rule('tag_id', 'numeric')
			// Lavel
			->label('item_id', __('Item ID'))
			->label('tag_id', __('Tag ID'))
		;

		return $validation;
	}

}