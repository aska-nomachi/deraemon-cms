<?php

defined('SYSPATH') OR die('No direct script access.');

return array(
	'tag_error' => '"{{" and "}}" are not same.',
	'if_tag_error' => '"{{#...}}" not closed.',
	'notif_tag_error' => '"{{^...}}" not closed.',
	'foreach_tag_error' => '"{{*...}}" not closed.',

	'login_success' => ':user logged in.',
	'login_failed' => 'Login failed.',
	'login_failed_messages' => array(
		'Please check your username and password.',
	),
	'login_refusal' => 'Login failed.',
	'login_refusal_messages' => array(
		'Login was refused.',
	),
);
