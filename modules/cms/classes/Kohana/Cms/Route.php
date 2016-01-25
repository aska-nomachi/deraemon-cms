<?php

defined('SYSPATH') OR die('No direct script access.');

class Kohana_Cms_Route {

	/**
	 * Route
	 *
	 * @return Route
	 */
	public static function write()
	{
		// Get backend name
		$backend_name = Cms_Helper::settings('backend_name');

		// Backend Auth
		Route::set('backend_auth', $backend_name.'/<action>', array(
				'action' => '(directuser|login|logout)',
			))
			->defaults(array(
				'directory' => 'backend',
				'controller' => 'auth',
		));

		// Backend Media
		Route::set('backend_media', $backend_name.'/media(/<stuff>)', array('stuff' => '.*'))
			->defaults(array(
				'directory' => 'backend',
				'controller' => 'media',
				'action' => 'index'
		));

		// Backend items
		Route::set('backend_items', $backend_name.'/items/<division>(/<action>(/<key>))')
			->filter(function($route, $params, $request){
				foreach ($params as &$param)
				{
					$param = str_replace('-', '_', $param);
				}
				return $params;
			})
			->defaults(array(
				'directory' => 'backend',
				'controller' => 'items',
				'action' => 'index'
		));

		// Backend
		Route::set('backend', $backend_name.'(/<controller>(/<action>(/<key>)))')
			->filter(function($route, $params, $request){
				foreach ($params as &$param)
				{
					$param = str_replace('-', '_', Text::ucfirst($param));
				}
				return $params;
			})
			->defaults(array(
				'directory' => 'backend',
				'controller' => 'home',
				'action' => 'index'
		));

		// Media
		Route::set('media', 'media(/<stuff>)', array('stuff' => '.*'))
			->defaults(array(
				'controller' => 'media',
				'action' => 'index'
		));

		// Imagefly
		// imagefly/1/w253-h253-p/test4.jpg
		Route::set('imagefly', 'imagefly(/<stuff>)', array('stuff' => '.*'))
			->defaults(array(
				'controller' => 'imagefly',
				'action' => 'index'
		));

		// Item
		Route::set('item', '<stuff>', array('stuff' => '.*'))
			->filter(function($route, $params, $request) {
				foreach ($params as &$param)
				{
					$param = str_replace('-', '_', Text::ucfirst($param));
				}

				$stuffs = explode('/', $params['stuff']);
				$end_staff = end($stuffs);
				$segment = substr($end_staff, 0, strlen($end_staff) - (strpos($end_staff, '.') - 1));

				if (!$segment)
				{
					$segment = Cms_Helper::settings('home_page');
				}

				$params['segment'] = $segment;

				$item = (bool) DB::select('id')
					->from('items')
					->where('segment', '=', $segment)
					->execute()
					->get('id');

				if (!$item)
				{
					return FALSE;
				}

				return $params;
				})
			->defaults(array(
				'controller' => 'item',
				'action' => 'index',
		));
	}

}
