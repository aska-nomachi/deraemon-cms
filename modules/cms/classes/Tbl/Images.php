<?php

defined('SYSPATH') or die('No direct script access.');

class Tbl_Images extends Tbl {

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
			'segment',
			'ext',
			'name',
			'description',
			'order',
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
	public function validate_upload($data)
	{
		$validation = Validation::factory($data)
			// rule
			->rule('image_file', 'Upload::not_empty')
			->rule('image_file', 'Upload::size', array(':value', '1M'))
			->rule('image_file', 'Upload::type', array(':value', array('jpg', 'png', 'gif')))
			->rule('image_file', 'Upload::valid')
			->rule('item_id', 'not_empty')
			->rule('segment', 'not_empty')
			->rule('segment', 'max_length', array(':value', '200'))
			->rule('segment', 'regex', array(':value', '/^[a-z0-9_]+$/'))
			->rule('segment', array($this, 'uniquely'), array(':field', ':value', $data['item_id']))
			->rule('ext', 'not_empty')
			->rule('ext', 'in_array', array(':value', array('.jpg', '.png', '.gif')))
			->rule('name', 'max_length', array(':value', '200'))
			->rule('order', 'numeric')
			// Lavel
			->label('item_id', __('Item ID'))
			->label('image file', 'Image File')
			->label('segment', __('Segment'))
			->label('ext', __('Ext'))
			->label('name', __('Name'))
			->label('order', __('Order'))
		;
		return $validation;
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
			->rule('segment', 'not_empty')
			->rule('segment', 'max_length', array(':value', '200'))
			->rule('segment', 'regex', array(':value', '/^[a-z0-9_]+$/'))
			->rule('segment', array($this, 'uniquely'), array(':field', ':value'))
			->rule('ext', 'not_empty')
			->rule('ext', 'in_array', array(':value', array('.jpg', '.png', '.gif')))
			->rule('name', 'max_length', array(':value', '200'))
			->rule('order', 'numeric')
			// Lavel
			->label('item_id', __('Item ID'))
			->label('segment', __('Segment'))
			->label('ext', __('Ext'))
			->label('name', __('Name'))
			->label('order', __('Order'))
		;

		return $validation;
	}

	/**
	 * Validation image uniquly
	 *
	 * @param string $field
	 * @param string $value
	 * @return boolean
	 */
	public function uniquely($field, $value, $item_id = NULL)
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

		// $thisのitem_idがある場合
		if (isset($this->item_id))
		{
			$item_id = $this->item_id;
		}
		// $item_idもないとき
		elseif (!$item_id)
		{
			return false;
		}

		return !(bool) DB::select()
				->from($this->_table)
				->where('item_id', '=', $item_id)
				->where($field, '=', $value)
				->execute()
				->count();
	}

// Image rotate Todo:: 複数イメージの場合！？
//			$files = array();
//			foreach ($_failes as $image_name => $image_keys)
//			{
//				foreach ($image_keys as $key => $value)
//				{
//					$i = 0;
//					foreach ($value as $val)
//					{
//						$files[$image_name][$i][$key] = $val;
//						$i++;
//					}
//				}
//			}

}