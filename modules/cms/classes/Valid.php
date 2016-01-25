<?php

defined('SYSPATH') OR die('No direct script access.');

class Valid extends Kohana_Valid {

	/**
	 * call_back -> array_in_array
	 * parm -> array(':value', array('スポーツ', '伝統芸能', '雑貨', '観光', 'ビューティー', 'グルメ', 'ファッション', 'ライフスタイル', 'ビジネス'))
	 *
	 * @param string $value
	 * @param array $array
	 * @return boolean
	 */
	public static function array_in_array($value, $array)
	{
		$result = TRUE;
		$value = is_array($value) ? $value : array($value);
		foreach ($value as $val)
		{
			if (!in_array($val, $array))
			{
				$result = FALSE;
				break;
			}
		}
		return $result;
	}

	/**
	 * call_back -> array_count_orlower
	 * parm -> array(':value', 3)
	 *
	 * @param array $value
	 * @param int $number
	 * @return int
	 */
	public static function array_count_orlower($value, $number)
	{
		$value = is_array($value) ? $value : array($value);

		return count($value) <= $number;
	}

	/**
	 * call_back -> array_count_orhigher
	 * parm -> array(':value', 3)
	 *
	 * @param array $value
	 * @param int $number
	 * @return int
	 */
	public static function array_count_orhigher($value, $number)
	{
		$value = is_array($value) ? $value : array($value);

		return count($value) >= $number;
	}

}
