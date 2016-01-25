<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * @package    Kohana
 * @category   Exceptions
 * @author     kohx
 */
class Warning_Exception extends Kohana_Exception {

	/**
	 * @var  object  Warning instance
	 */
	public $array;

	/**
	 * @param  Warning   $array      Validation object
	 * @param  string       $message    error message
	 * @param  array        $values     translation variables
	 * @param  int          $code       the exception code
	 */
	public function __construct($message = '', array $values = NULL, $code = 0, Exception $previous = NULL)
	{
		parent::__construct($message, $values, $code, $previous);
	}

}
