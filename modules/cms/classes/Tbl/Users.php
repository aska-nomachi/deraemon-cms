<?php

defined('SYSPATH') or die('No direct script access.');

class Tbl_Users extends Tbl {

	/**
	 * Construct
	 *
	 * @param string $name
	 */
	public function __construct()
	{
		$this->_columns = array(
			'id',
			'email',
			'username',
			'password',
			'logins',
			'last_login',
			'reset_key',
			'ext',
			'is_block',
		);

		$this->_unchangeable = array(
			'id',
		);

		parent::__construct();
	}

	/**
	 * Override create
	 *
	 * @param array $data
	 * @param type $validation
	 * @return this
	 */
	public function create($data, $validate_function = 'validate')
	{
		// Validation
		$validation = call_user_func(array($this, $validate_function), $data);

		// Check validation
		if (!$validation->check())
		{
			throw new Validation_Exception($validation);
		}

		// Build data
		foreach ($data as $key => $value)
		{
			if (!in_array($key, $this->_columns))
			{
				unset($data[$key]);
			}
		}

		// Hash password
		$data['password'] = Auth::instance()->hash_password($data['password']);

		// Insert
		list($id, $total_row) = DB::insert($this->_table)
			->columns(array_keys($data))
			->values(array_values($data))
			->execute();

		// Reload
		$this->get($id);

		return $this;
	}

	/**
	 * Override update
	 *
	 * @param   array column value data
	 * @return  this
	 */
	public function update($data = array(), $validate_function = 'validate')
	{
		// If not loaded
		if (!$this->_loaded)
		{
			throw new Kohana_Exception('this is not loaded.');
		}

		// Build data
		$current_data = array();

		foreach ($this->_columns as $key)
		{
			$current_data[$key] = $this->{$key};
		}

		// Merge data
		$merge_data = Arr::merge($current_data, $data);

		// Validation
		$validation = call_user_func(array($this, $validate_function), $merge_data);

		// Check validation
		if (!$validation->check())
		{
			throw new Validation_Exception($validation);
		}

		if (isset($data['password']))
		{
			// Hash password
			$merge_data['password'] = Auth::instance()->hash_password($data['password']);
		}

		// Filter
		foreach ($merge_data as $key => $value)
		{
			if (!in_array($key, $this->_columns))
			{
				unset($merge_data[$key]);
			}
			if (in_array($key, $this->_unchangeable))
			{
				unset($merge_data[$key]);
			}
		}

		// Update
		DB::update($this->_table)
			->set($merge_data)
			->where('id', '=', $this->id)
			->execute();

		// Reload
		$this->get($this->id);

		return $this;
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
			->rule('email', 'not_empty')
			->rule('email', 'max_length', array(':value', '254'))
			->rule('email', 'email')
			->rule('email', array($this, 'uniquely'), array(':field', ':value'))
			->rule('username', 'not_empty')
			->rule('username', 'regex', array(':value', '/^[a-z0-9_]+$/'))
			->rule('username', 'max_length', array(':value', '32'))
			->rule('username', array($this, 'uniquely'), array(':field', ':value'))
			->rule('password', 'not_empty')
			->rule('password', 'min_length', array(':value', 8))
			// Lavel
			->label('email', __('Email'))
			->label('segment', __('Segment'))
			->label('username', __('Username'))
			->label('password', __('Password'))
		;

		return $validation;
	}

	/**
	 * Validate with avatar
	 *
	 * @abstract Model_Table
	 * @param array $data
	 * @return Validation
	 * @throws Validation_Exception
	 */
	public function validate_with_avatar($data)
	{
		$validation = Validation::factory($data)
			// rule
			->rule('email', 'not_empty')
			->rule('email', 'max_length', array(':value', '254'))
			->rule('email', 'email')
			->rule('email', array($this, 'uniquely'), array(':field', ':value'))
			->rule('username', 'not_empty')
			->rule('username', 'regex', array(':value', '/^[a-z0-9_]+$/'))
			->rule('username', 'max_length', array(':value', '32'))
			->rule('username', array($this, 'uniquely'), array(':field', ':value'))
			->rule('password', 'not_empty')
			->rule('password', 'min_length', array(':value', 8))
			// rule for avatar
			->rule('avatar', 'Upload::not_empty')
			->rule('avatar', 'Upload::size', array(':value', '1M'))
			->rule('avatar', 'Upload::type', array(':value', array('jpg', 'png', 'gif')))
			->rule('avatar', 'Upload::valid')
			->rule('ext', 'not_empty')
			->rule('ext', 'in_array', array(':value', array('.jpg', '.png', '.gif')))
			// Lavel
			->label('email', __('Email'))
			->label('segment', __('Segment'))
			->label('username', __('Username'))
			->label('password', __('Password'))
			// Lavel for avatar
			->label('image file', 'Image File')
			->label('ext', __('Ext'))
		;

		return $validation;
	}

	/**
	 * Add roles
	 *
	 * @param   string role names
	 * @return  this
	 */
	public function add_roles($role_names)
	{
		// If not loaded
		if (!$this->_loaded)
		{
			throw new Kohana_Exception('this is not loaded.');
		}

		if ($role_names)
		{
			// Get role id
			$role_id = Tbl::factory('roles')
				->where('name', '=', $role_names)
				->read('id');

			// Build sql
			Tbl::factory('roles_users')
				->create(array(
					'user_id' => $this->id,
					'role_id' => $role_id,
			));

			// Reload
			$this->get($this->id);
		}

		return $this;
	}

	/**
	 * validation emailがデータベースにあればtrue
	 */
	public static function has_email($value)
	{
		return (bool) DB::select()
				->from('users')
				->where('email', '=', $value)
				->execute()
				->count();
	}

	/**
	 * validation check_pass
	 */
	public static function check_pass($present)
	{
		$logged_in_user = Auth::instance()->get_user();
		if ($logged_in_user AND ( $logged_in_user->password === Auth::instance()->hash_password($present)))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
}
