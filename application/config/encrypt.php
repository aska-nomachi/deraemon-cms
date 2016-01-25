<?php
$settings= (object) Tbl::factory('settings')
	->or_where('key', '=', 'encrypt_key')
	->or_where('key', '=', 'encrypt_cipher')
	->or_where('key', '=', 'encrypt_mode')
	->read()
	->as_array('key', 'value');

return array(
	'default' => array(
		'key' => $settings->encrypt_key,
		'cipher' => $settings->encrypt_cipher,
		'mode' => $settings->encrypt_mode,
	),
);