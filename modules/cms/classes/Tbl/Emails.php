<?php

defined('SYSPATH') or die('No direct script access.');

class Tbl_Emails extends Tbl {

	/**
	 * Construct
	 *
	 * @param string $name
	 */
	public function __construct()
	{
		$this->_columns = array(
			'id',
			'segment',
			'name',
			'description',
			'receive_subject',
			'receive_email_type',
			'user_name_field',
			'user_address_field',
			'confirm_subject',
			'confirm_email_type',
			'admin_name',
			'admin_address',
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
			->rule('segment', 'not_empty')
			->rule('segment', 'max_length', array(':value', '200'))
			->rule('segment', 'regex', array(':value', '/^[a-z0-9_]+$/'))
			->rule('segment', array($this, 'uniquely'), array(':field', ':value'))
			->rule('name', 'not_empty')
			->rule('name', 'max_length', array(':value', '200'))

			->rule('receive_subject', 'not_empty')
			->rule('receive_email_type', 'not_empty')
			->rule('user_name_field', 'max_length', array(':value', '45'))
			->rule('user_address_field', 'max_length', array(':value', '45'))

			->rule('confirm_subject', 'not_empty')
			->rule('confirm_email_type', 'not_empty')
			->rule('admin_name', 'not_empty')
			->rule('admin_name', 'max_length', array(':value', '200'))
			->rule('admin_address', 'not_empty')
			->rule('admin_address', 'email')
			->rule('admin_address', 'max_length', array(':value', '200'))

			// Lavel
			->label('segment', __('Segment'))
			->label('name', __('Name'))
			->label('receive_subject', __('Receive subject'))
			->label('receive_email_type', __('Receive email type'))
			->label('user_name_field', __('User name field'))
			->label('user_address_field', __('User address field'))
			->label('confirm_subject', __('Confirm subject'))
			->label('confirm_email_type', __('Confirm email type'))
			->label('admin_name', __('Admin name'))
			->label('admin_address', __('Admin address'))
		;

		return $validation;
	}

}
