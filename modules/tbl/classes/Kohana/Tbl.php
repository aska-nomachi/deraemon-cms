<?php

defined('SYSPATH') or die('No direct script access.');

abstract class Kohana_Tbl {

	protected $_table = NULL;
	protected $_columns = array();
	protected $_unchangeable = array();
	protected $_loaded = FALSE;
	protected $_read = NULL;

	/**
	 * Database methods pending
	 *
	 * @var array
	 */
	protected $_pending = array();

	/**
	 * Create a new table instance.
	 *
	 *     $table = Table::factory($name);
	 *
	 * @param  string $name table name
	 * @return Tbl
	 */
	public static function factory($name)
	{
		if ($name)
		{
			$class = 'Tbl_'.Text::ucfirst($name, '_');
		}
		else
		{
			$class = get_called_class();
		}

		return new $class();
	}

	/**
	 * Construct
	 *
	 * @return  void
	 */
	public function __construct()
	{
		$this->_table = self::tablename();
	}

	/**
	 * table name
	 */
	public static function tablename()
	{
		return strtolower(substr(get_called_class(), 4));
	}

	/**
	 * Create
	 *
	 * @param   array column value data
	 * @return  this
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
	 * Set value to this
	 *
	 * @return  this
	 */
	public function set($key, $value)
	{
		if (in_array($key, $this->_columns))
		{
			$this->{$key} = $value;
		}

		return $this;
	}

	/**
	 * Get
	 *
	 * @param   integer id
	 * @return  this
	 */
	public function get($id = NULL)
	{
		// Build sql
		$sql = DB::select()->from($this->_table);

		// If param has id
		if ($id)
		{
			$sql->where('id', '=', $id);
		}
		else
		{
			foreach ($this->_pending as $method)
			{
				$name = $method['name'];
				$args = $method['args'];

				call_user_func_array(array($sql, $name), $args);
			}
		}

		// Execute
		$results = $sql
			->as_object()
			->execute();

		// If results is not one
		if ($results->count() > 1)
		{
			throw new Kohana_Exception('The result is more than 1.');
		}

		$result = $results->current();

		// If there is not result
		if (!$result)
		{
			return FALSE;
		}

		// Set to this
		foreach ($result as $key => $value)
		{
			{
				$this->{$key} = $value;
			}
		}

		// Set loaded true
		$this->_loaded = TRUE;

		return $this;
	}

	/**
	 * Update
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
		return $this;
	}

	/**
	 * Delete
	 *
	 * @return  this
	 */
	public function delete()
	{
		// If not loaded
		if (!$this->_loaded)
		{
			throw new Kohana_Exception('this is not loaded.');
		}

		// Delete
		DB::delete($this->_table)
			->where('id', '=', $this->id)
			->execute();

		return $this;
	}

	/**
	 * Read
	 *
	 * @param mixed $one
	 * @return object
	 */
	public function read($one = FALSE)
	{
		$sql = DB::select()
			->from($this->_table);

		// Process pending database method calls
		foreach ($this->_pending as $method)
		{
			$name = $method['name'];
			$args = $method['args'];

			call_user_func_array(array($sql, $name), $args);
		}

		if ($one)
		{
			if ($one === TRUE OR $one === 1)
			{
				return $sql
						->limit(1)
						->as_object()
						->execute()
						->current();
			}
			else
			{
				return $sql
						->limit(1)
						->as_object()
						->execute()
						->get($one);
			}
		}
		else
		{
			$this->_read = $sql
				->as_object()
				->execute();

			return $this;
		}
	}

	public function as_array($key = NULL, $value = NULL)
	{
		return $this->_read->as_array($key, $value);
	}

	public function count($key = NULL, $value = NULL)
	{
		return count($this->_read->as_array(NULL, 'id'));
	}

	/**
	 * Validate data based on rules
	 * Throw Validation_Exception if failed validating data
	 *
	 * 	public function validate_update($data)
	 * 	{
	 * 		$validation = Validation::factory($data)
	 * 			->rule('email', 'not_empty')
	 * 			->rule('username', 'not_empty')
	 * 			->label('email', __('Email'))
	 * 			->label('username', __('Username'));
	 *
	 * 		return $validation;
	 * 	}
	 *
	 * @throw		Validation_Exception
	 * @return		Validation
	 */
	abstract public function validate($data);
	/**
	 * Validation uniquly
	 * This validation is for in the Model_table.
	 *
	 * @param string $field
	 * @param string $value
	 * @return boolean
	 */
	public function uniquely($field, $value)
	{
		// フィールドがあって
		if (isset($this->{$field}))
		{
			// 新しい値ともとの値が同じの時はOK
			if ($this->{$field} == $value)
			{
				return TRUE;
			}
		}

		return !(bool) DB::select()
				->from($this->_table)
				->where($field, '=', $value)
				->execute()
				->count();
	}

	/**
	 * Validation unique
	 * Todo:: つかってない？ staticのuniquely
	 *
	 * @param string $field
	 * @param string $value
	 * @param string $present_value
	 * @return boolean
	 */
	public static function unique($field, $value, $present_value = NULL)
	{
		if ($present_value)
		{
			if ($present_value == $value)
			{
				return TRUE;
			}
		}

		return !(bool) DB::select()
				->from(self::tablename())
				->where($field, '=', $value)
				->execute()
				->count();
	}

	/**
	 * Query bilder for read
	 * @return $this
	 *
	 * ->select('received_comments.*')
	 * ->select(array('items.segment', 'item_segment'))
	 */
	// Select
	public function select($columns = NULL)
	{
		$this->_pending[] = array('name' => 'select', 'args' => array($columns));
		return $this;
	}

	// Where
	public function where($column, $op, $value)
	{
		$this->_pending[] = array('name' => 'where', 'args' => array($column, $op, $value));
		return $this;
	}

	public function and_where($column, $op, $value)
	{
		$this->_pending[] = array('name' => 'and_where', 'args' => array($column, $op, $value));
		return $this;
	}

	public function or_where($column, $op, $value)
	{
		$this->_pending[] = array('name' => 'or_where', 'args' => array($column, $op, $value));
		return $this;
	}

	public function where_open()
	{
		$this->_pending[] = array('name' => 'where_open', 'args' => array());
		return $this;
	}

	public function and_where_open()
	{
		$this->_pending[] = array('name' => 'and_where_open', 'args' => array());
		return $this;
	}

	public function or_where_open()
	{
		$this->_pending[] = array('name' => 'or_where_open', 'args' => array());
		return $this;
	}

	public function where_close()
	{
		$this->_pending[] = array('name' => 'where_close', 'args' => array());
		return $this;
	}

	public function and_where_close()
	{
		$this->_pending[] = array('name' => 'and_where_close', 'args' => array());
		return $this;
	}

	public function or_where_close()
	{
		$this->_pending[] = array('name' => 'or_where_close', 'args' => array());
		return $this;
	}

	// Join on
	public function join($table, $type = NULL)
	{
		$this->_pending[] = array('name' => 'join', 'args' => array($table, $type));
		return $this;
	}

	public function on($c1, $op, $c2)
	{
		$this->_pending[] = array('name' => 'on', 'args' => array($c1, $op, $c2));
		return $this;
	}

	// Group
	public function group_by($columns)
	{
		$this->_pending[] = array('name' => 'group_by', 'args' => array($columns));
		return $this;
	}

	public function having($column, $op, $value = NULL)
	{
		$this->_pending[] = array('name' => 'having', 'args' => array($column, $op, $value));
		return $this;
	}

	public function or_having($column, $op, $value = NULL)
	{
		$this->_pending[] = array('name' => 'or_having', 'args' => array($column, $op, $value));
		return $this;
	}

	public function having_open()
	{
		$this->_pending[] = array('name' => 'having_open', 'args' => array());
		return $this;
	}

	public function and_having_open()
	{
		$this->_pending[] = array('name' => 'and_having_open', 'args' => array());
		return $this;
	}

	public function or_having_open()
	{
		$this->_pending[] = array('name' => 'or_having_open', 'args' => array());
		return $this;
	}

	public function having_close()
	{
		$this->_pending[] = array('name' => 'having_close', 'args' => array());
		return $this;
	}

	public function and_having_close()
	{
		$this->_pending[] = array('name' => 'having_close', 'args' => array());
		return $this;
	}

	public function or_having_close()
	{
		$this->_pending[] = array('name' => 'having_close', 'args' => array());
		return $this;
	}

	// order
	public function order_by($column, $direction = NULL)
	{
		$this->_pending[] = array('name' => 'order_by', 'args' => array($column, $direction));
		return $this;
	}

	public function limit($number)
	{
		$this->_pending[] = array('name' => 'limit', 'args' => array($number));
		return $this;
	}

	public function offset($number)
	{
		$this->_pending[] = array('name' => 'offset', 'args' => array($number));
		return $this;
	}

}
