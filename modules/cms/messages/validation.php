<?php defined('SYSPATH') OR die('No direct script access.');

return array(
	'alpha'         => ':field must contain only letters',
	'alpha_dash'    => ':field must contain only numbers, letters and dashes',
	'alpha_numeric' => ':field must contain only letters and numbers',
	'color'         => ':field must be a color',
	'credit_card'   => ':field must be a credit card number',
	'date'          => ':field must be a date',
	'decimal'       => ':field must be a decimal with :param2 places',
	'digit'         => ':field must be a digit',
	'email'         => ':field must be an email address',
	'email_domain'  => ':field must contain a valid email domain',
	'equals'        => ':field must equal :param2',
	'exact_length'  => ':field must be exactly :param2 characters long',
	'in_array'      => ':field must be one of the available options',
	'ip'            => ':field must be an ip address',
	'matches'       => ':field must be the same as :param3',
	'min_length'    => ':field must be at least :param2 characters long',
	'max_length'    => ':field must not exceed :param2 characters long',
	'not_empty'     => ':field must not be empty',
	'numeric'       => ':field must be numeric',
	'phone'         => ':field must be a phone number',
	'range'         => ':field must be within the range of :param2 to :param3',
	'regex'         => ':field does not match the required format',
	'url'           => ':field must be a url',

	// Valid class
	'array_in_array'					=> ':field is not valid',
	'array_count_orlower'			=> ':field The field is :param2 or lower.',
	'array_count_orhigher'		=> ':field The field is :param2 or higher.',

	// Tbl class
	'uniquely'      => ':field must be unique',
	'unique'        => ':field must be unique',

	'valid'					=> ':field is not valid',
	'type'					=> ':field type must :param2',

	'segment_unique'=> ':field must be unique',

	'Upload::not_empty'=> ':field must not be empty',
	'Upload::type' => ':field type is not match.',
	'Upload::size' => ':field size is too large.',
	'Upload::valid' => ':field file is corrupted.',

	'Tbl_Users::has_email'=> ':field is not registered.',
	'Tbl_Users::check_pass'=> ':field is different.',

	// ZB Valid class
	'unique_blog_url'=> ':field must be unique',
);
