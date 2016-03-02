<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Divisions extends Controller_Backend_Template {

	/**
	 * Before
	 */
	public function before()
	{
		parent::before();

		// Local menus
		$this->local_menus = array(
			'index' => array('name' => 'index', 'url' => URL::site("{$this->settings->backend_name}/divisions", 'http')),
		);

		// local menus set current
		foreach ($this->local_menus as $key => &$value)
		{
			if ($key == strtolower($this->request->action()))
			{
				$value['current'] = TRUE;
			}
		}

		// Set partial
		$this->partials['local_menu'] = Tpl::get_file('local_menu', $this->settings->back_tpl_dir);
	}

	/**
	 * Action index
	 */
	public function action_index()
	{
		// If there are post
		if ($this->request->post())
		{
			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				// Create
				$division = Tbl::factory('divisions')
						->create($this->request->post());

				// Check items division directory and create items division directory
				// これはitemを入れるとこを作る
				Cms_Helper::make_dir($division->segment, $this->settings->item_dir);

				// Check images division directory and create images division directory
				// これはimageを入れるとこを作る
				Cms_Helper::make_dir($division->segment, $this->settings->image_dir . '/item');

				// Create division file これはディビジョンテンプレート
				Cms_Helper::set_file($division->segment, $this->settings->front_tpl_dir . $this->settings->front_theme . '/division', '{{>shape_content}}');

				// Database commit
				Database::instance()->commit();

				// Clear post
				$this->request->post(array());

				// Add success notice
				Notice::add(Notice::SUCCESS, Kohana::message('general', 'create_success'));
				
				// Redirect for new division
				$this->redirect(Request::current()->url('http'));
			}
			catch (HTTP_Exception_302 $e)
			{
				$this->redirect($e->location());
			}
			catch (Validation_Exception $e)
			{
				// Database rollback
				Database::instance()->rollback();

				// Add validation notice
				Notice::add(Notice::VALIDATION, Kohana::message('general', 'create_failed'), NULL, $e->errors('validation'));
			}
			catch (Exception $e)
			{
				// Database rollback
				Database::instance()->rollback();

				// Add error notice
				Notice::add(
						Notice::ERROR, $e->getMessage()
				);
			}
		}

		// Get divisions
		$divisions = Tbl::factory('divisions')
				->select('divisions.*')
				->select(array('wrappers.segment', 'wrapper_segment'))
				->select(array('wrappers.name', 'wrapper_name'))
				->select(array('wrappers.content_type', 'wrapper_content_type'))
				->join('wrappers')->on('divisions.wrapper_id', '=', 'wrappers.id')
				->read()
				->as_array();

		foreach ($divisions as $division)
		{
			$division->edit_url = URL::site("{$this->settings->backend_name}/divisions/edit/{$division->id}", 'http');
		}

		// Get wrappers
		$wrappers = Tbl::factory('wrappers')
				->read()
				->as_array();
		
		/**
		 * View
		 */
		$content_file = Tpl::get_file('index', $this->settings->back_tpl_dir . '/divisions', $this->partials);

		$this->content = Tpl::factory($content_file)
				->set('divisions', $divisions)
				->set('wrappers', $wrappers)
				->set('post', $this->request->post());
	}

	/**
	 * Action edit
	 */
	public function action_edit()
	{
		// Get id from param, if there is nothing then throw to 404
		$id = $this->request->param('key');
		if (!$id)
		{
			throw HTTP_Exception::factory(404);
		}

		// Get division, if there is nothing then throw to 404
		$division = Tbl::factory('divisions')->get($id);
		if (!$division)
		{
			throw HTTP_Exception::factory(404);
		}

		// Get wrapper
		$wrapper = Tbl::factory('wrappers')
				->where('id', '=', $division->wrapper_id)
				->read(1);

		// Direct set to division
		$division->wrapper_segment = $wrapper->segment;
		$division->wrapper_name = $wrapper->name;
		$division->wrapper_content_type = $wrapper->content_type;

		// Get content from file and direct set to division
		$division->content = Tpl::get_file($division->segment, $this->settings->front_tpl_dir . $this->settings->front_theme . '/division');
		$division->delete_url = URL::site("{$this->settings->backend_name}/divisions/delete/{$division->id}", 'http');

		// Save old name
		$oldname = $division->segment;

		// Get wrappers
		$wrappers = Tbl::factory('wrappers')
				->read()
				->as_array();

		// If there are post
		if ($this->request->post())
		{
			// Set post to division
			$division->wrapper_id = $this->request->post('wrapper_id');
			$division->segment = $this->request->post('segment');
			$division->name = $this->request->post('name');
			$division->content = $this->request->post('content');

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				// Update
				Tbl::factory('divisions')
						->get($division->id)
						->update(array(
							'wrapper_id' => $this->request->post('wrapper_id'),
							'segment' => $this->request->post('segment'),
							'name' => $this->request->post('name'),
				));

				// New name
				$newname = $division->segment;

				// Rename items/division/directory name
				Cms_Helper::rename_dir($oldname, $newname, $this->settings->item_dir);

				// Rename images/division/directory name
				Cms_Helper::rename_dir($oldname, $newname, $this->settings->image_dir . '/item');

				// rename theme/.../division/division file
				Cms_Helper::rename_file($oldname, $newname, $this->settings->front_tpl_dir . $this->settings->front_theme . '/division');

				// Update file
				Cms_Helper::set_file($newname, $this->settings->front_tpl_dir . $this->settings->front_theme . '/division', $this->request->post('content'));

				// Database commit
				Database::instance()->commit();

				// Add success notice
				Notice::add(Notice::SUCCESS, Kohana::message('general', 'update_success'));

				// Redirect
				$this->redirect(URL::site("{$this->settings->backend_name}/divisions/edit/{$division->id}", 'http'));
			}
			catch (HTTP_Exception_302 $e)
			{
				$this->redirect($e->location());
			}
			catch (Validation_Exception $e)
			{
				// Database rollback
				Database::instance()->rollback();

				// Add validation notice
				Notice::add(Notice::VALIDATION, Kohana::message('general', 'update_failed'), NULL, $e->errors('validation'));
			}
			catch (Exception $e)
			{
				// Database rollback
				Database::instance()->rollback();

				// Add error notice
				Notice::add(
						Notice::ERROR, $e->getMessage()
				);
			}
		}

		/**
		 * View
		 */
		$content_file = Tpl::get_file('edit', $this->settings->back_tpl_dir . '/divisions', $this->partials);

		$this->content = Tpl::factory($content_file)
				->set('division', $division)
				->set('wrappers', $wrappers);
	}

	/**
	 * Action delete
	 */
	public function action_delete()
	{
		// Auto render off
		$this->auto_render = FALSE;

		// Get id from param, if there is nothing then throw to 404
		$id = $this->request->param('key');
		if (!$id)
			throw HTTP_Exception::factory(404);

		// Get division, if there is nothing then throw to 404
		$division = Tbl::factory('divisions')->get($id);
		if (!$division)
			throw HTTP_Exception::factory(404);

		// Database transaction start
		Database::instance()->begin();

		// Try
		try
		{
			/**
			 * Check other tables
			 */
			// used by items
			$used_items = (bool) Tbl::factory('items')
							->where('division_id', '=', $division->id)
							->read()
							->count();

			// used by categories
			$used_categories = (bool) Tbl::factory('categories')
							->where('division_id', '=', $division->id)
							->read()
							->count();

			// used by fields
			$used_fields = (bool) Tbl::factory('fields')
							->where('division_id', '=', $division->id)
							->read()
							->count();

			// Build tables array
			$tables = array();
			if ($used_items)
				$tables[] = 'items';
			if ($used_categories)
				$tables[] = 'categories';
			if ($used_fields)
				$tables[] = 'fields';

			// If this division is used when throw to warning
			if ($used_items OR $used_categories OR $used_fields)
			{
				throw new Warning_Exception(Kohana::message('general', 'division_is_used'), array(':tables' => implode(', ', $tables)));
			}

			/**
			 * Delete
			 */
			// Delete file まずファイルを消す！
			$file_delete_success = Cms_Helper::delete_file($division->segment, $this->settings->front_tpl_dir . $this->settings->front_theme . '/division');
			if ($file_delete_success)
			{
				Cms_Helper::delete_dir($division->segment, $this->settings->item_dir);
				Cms_Helper::delete_dir($division->segment, $this->settings->image_dir . '/item');
			}

			// Delete
			$division->delete();

			// Database commit
			Database::instance()->commit();

			// Add success notice
			Notice::add(Notice::SUCCESS, Kohana::message('general', 'delete_success'));

			$this->redirect(URL::site("{$this->settings->backend_name}/divisions/index", 'http'));
		}
		catch (HTTP_Exception_302 $e)
		{
			$this->redirect($e->location());
		}
		catch (Validation_Exception $e)
		{
			// Database rollback
			Database::instance()->rollback();

			// Add validation notice
			Notice::add(Notice::VALIDATION, Kohana::message('general', 'delete_failed'), NULL, $e->errors('validation'));
		}
		catch (Warning_Exception $e)
		{
			// Database rollback
			Database::instance()->rollback();

			// Add
			Notice::add(Notice::WARNING, $e->getMessage());
		}
		catch (Exception $e)
		{
			// Database rollback
			Database::instance()->rollback();

			// Add error notice
			Notice::add(
					Notice::ERROR, $e->getMessage()//.'<br>'.$e->getFile().'<br>'.$e->getLine().'<br>'
			);
		}

		// Redirect to wrapper edit
		$this->redirect(URL::site("{$this->settings->backend_name}/divisions/edit/{$division->id}", 'http'));
	}

	/**
	 * After
	 */
	public function after()
	{
		parent::after();
	}

}
