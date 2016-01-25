<?php

defined('SYSPATH') OR die('No direct access allowed.');

$settings = (object) Tbl::factory('settings')
		->or_where('key', '=', 'temp_dir')
		->or_where('key', '=', 'temp_pre')
		->or_where('key', '=', 'tpl_func')
		->read()
		->as_array('key', 'value');

return array(
	'temp_dir' => $settings->temp_dir,
	'temp_pre' => $settings->temp_pre,
	'tpl_func' => $settings->tpl_func,
);
