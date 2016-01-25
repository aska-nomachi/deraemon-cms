<?php

defined('SYSPATH') or die('No direct script access.');
//general
return array(
	// database
	'create_success' => 'Create success.',
	'create_failed' => 'Create failed. Please check these following errors.',
	'update_success' => 'Update success.',
	'update_failed' => 'Update failed. Please check these following errors.',
	'delete_success' => 'Delete success.',
	'no_delete' => 'No changes detected. Please choose the :text to delete.',
	'delete_failed' => 'Delete failed. :message',
	// authority
	'no_authority' => 'It cannot be operated by this authority.',
	// information
	'nothing' => 'Since the :no_create is not create, :no_display is not displayed.',
	'please' => 'Please create a :no_create.',
	// warning
	'shape_is_used' => 'This shape currently used by items.',
	'division_is_used' => 'This division currently used by :tables.',
	'wrapper_is_used' => 'This wrapper currently used by division.',
	'image_is_used_for_main' => 'image ID::id currently used by item main image.',
	'relation_delete' => 'Deletion of the field will also delete the :text of each item.',
	// error
	'not_exist' => ':text is not exist. Please try again.',
);
