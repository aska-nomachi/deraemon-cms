<?php

defined('SYSPATH') or die('No direct script access.');

class Tbl_Items extends Tbl {

	/**
	 * Construct
	 *
	 * @param string $name
	 */
	public function __construct()
	{
		$this->_columns = array(
			'id',
			'division_id',
			'shape_segment',
			'image_id',
			'user_id',
			'parent_id',
			'segment',
			'title',
			'catch',
			'keywords',
			'description',
			'summary',
			'order',
			'is_active',
			'issued',
			'created',
			'send_comment_is_on',
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
			->rule('division_id', 'not_empty')
			->rule('segment', 'not_empty')
			->rule('segment', 'max_length', array(':value', '200'))
			->rule('segment', 'regex', array(':value', '/^[a-z0-9_]+$/'))
			->rule('segment', array($this, 'uniquely'), array(':field', ':value'))
			->rule('title', 'not_empty')
			->rule('title', 'max_length', array(':value', '200'))
			->rule('order', 'numeric')
			->rule('is_active', 'in_array', array(':value', array(0, 1)))
			->rule('issued', 'date')
			->rule('created', 'date')
			->rule('send_comment_is_on', 'in_array', array(':value', array(0, 1)))
			// Lavel
			->label('division_id', __('Division ID'))
			->label('segment', __('Segment'))
			->label('title', __('Title'))
			->label('order', __('Order'))
			->label('is_active', __('Activate'))
			->label('issued', __('Issued'))
			->label('created', __('Created'))
			->label('send_comment_is_on', __('Send comment is ON'))
		;

		return $validation;
	}

}