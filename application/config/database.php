<?php

defined('SYSPATH') OR die('No direct access allowed.');

return array
	(
	'default' => array
		(
		'type' => 'MySQL',
		'connection' => array(
			'hostname' => 'localhost',
			'database' => 'default_emon',
			'username' => 'root',
			'password' => '',
			'persistent' => FALSE,
		),
		'table_prefix' => '',
		'charset' => 'utf8',
		'caching' => FALSE,
	),
);
