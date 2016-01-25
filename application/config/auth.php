<?php

defined('SYSPATH') OR die('No direct access allowed.');

$settings = (object) Tbl::factory('settings')
		->or_where('key', '=', 'auth_hash_method')
		->or_where('key', '=', 'auth_hash_key')
		->or_where('key', '=', 'auth_lifetime')
		->or_where('key', '=', 'auth_session_key')
		->read()
		->as_array('key', 'value');

return array(
	'driver' => 'Database',
	'hash_method' => $settings->auth_hash_method,
	'hash_key' => $settings->auth_hash_key,
	'lifetime' => Cms_Helper::sec($settings->auth_lifetime),
	'session_type' => Session::$default,
	'session_key' => $settings->auth_session_key,
);