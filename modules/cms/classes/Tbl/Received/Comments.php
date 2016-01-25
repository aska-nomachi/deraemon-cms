<?php

defined('SYSPATH') or die('No direct script access.');

class Tbl_Received_Comments extends Tbl {

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
			'user_id',
			'replay_id',
			'display_name',
			'subject',
			'content',
			'created',
			'is_accept',
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
			->rule('user_id', 'numeric')
			->rule('replay_id', 'numeric')
			->rule('display_name', 'max_length', array(':value', '200'))
			->rule('subject', 'max_length', array(':value', '200'))
			->rule('content', 'not_empty')
			->rule('content', 'max_length', array(':value', '400'))
			->rule('created', 'date')
			->rule('is_accept', 'in_array', array(':value', array(0, 1)))
			// Lavel
			->label('item_id', __('Item ID'))
			->label('user_id', __('User ID'))
			->label('replay_id', __('Replay ID'))
			->label('display_name', __('Display name'))
			->label('subject', __('Subject'))
			->label('content', __('Content'))
			->label('created', __('Created'))
			->label('is_accept', __('Is accept'))
		;

		return $validation;
	}

}