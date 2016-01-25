<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Wrappers extends Controller_Backend_Template {

	/**
	 * Before
	 */
	public function before()
	{
		parent::before();

		// Local menus
		$this->local_menus = array(
			'index' => array('name' => 'index', 'url' => URL::site("{$this->settings->backend_name}/wrappers", 'http')),
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
				$warapper = Tbl::factory('wrappers')
					->create($this->request->post());

				// Create file
				Cms_Helper::set_file("wrapper/{$warapper->segment}", $this->settings->front_tpl_dir, '{{>division_content}}');

				// Database commit
				Database::instance()->commit();

				// Clear post
				$this->request->post(array());

				// Add success notice
				Notice::add(Notice::SUCCESS, Kohana::message('general', 'create_success'));
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
					Notice::ERROR, $e->getMessage().'|'.$e->getFile().'|'.$e->getLine()
				);
			}
		}

		// Get wrappers
		$wrappers = Tbl::factory('wrappers')
			->read()
			->as_array();

		foreach ($wrappers as $wrapper)
		{
			$wrapper->edit_url = URL::site("{$this->settings->backend_name}/wrappers/edit/{$wrapper->id}", 'http');
		}

		/**
		 * View
		 */
		$content_file = Tpl::get_file('index', $this->settings->back_tpl_dir.'/wrappers', $this->partials);

		$this->content = Tpl::factory($content_file)
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
		if (!$id) throw HTTP_Exception::factory(404);

		// Get wrapper, if there is nothing then throw to 404
		$wrapper = Tbl::factory('wrappers')
			->where('id', '=', $id)
			->read(1);
		if (!$wrapper) throw HTTP_Exception::factory(404);

		// Get content from file and direct set to wrppaer
		$wrapper->content = Tpl::get_file($wrapper->segment, $this->settings->front_tpl_dir.'/wrapper');
		$wrapper->delete_url = URL::site("{$this->settings->backend_name}/wrappers/delete/{$wrapper->id}", 'http');

		// Save present segment
		$oldfile = "wrapper/{$wrapper->segment}";

		// If there are post
		if ($this->request->post())
		{
			// Set post to wrapper
			$wrapper->segment = $this->request->post('segment');
			$wrapper->name = $this->request->post('name');
			$wrapper->content_type = $this->request->post('content_type');
			$wrapper->content = $this->request->post('content');

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				// Update
				Tbl::factory('wrappers')
					->get($wrapper->id)
					->update(array(
						'segment' => $this->request->post('segment'),
						'name' => $this->request->post('name'),
						'content_type' => $this->request->post('content_type'),
				));

				// New file
				$newfile = "wrapper/{$wrapper->segment}";

				// rename file
				Cms_Helper::rename_file($oldfile, $newfile, $this->settings->front_tpl_dir);

				// Update file
				Cms_Helper::set_file($newfile, $this->settings->front_tpl_dir, $this->request->post('content'));

				// Database commit
				Database::instance()->commit();

				// Add success notice
				Notice::add(Notice::SUCCESS, Kohana::message('general', 'update_success'));
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
		$content_file = Tpl::get_file('edit', $this->settings->back_tpl_dir.'/wrappers', $this->partials);

		$this->content = Tpl::factory($content_file)
			->set('wrapper', $wrapper);
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
		if (!$id) throw HTTP_Exception::factory(404);

		// Get wrapper, if there is nothing then throw to 404
		$wrapper = Tbl::factory('wrappers')->get($id);
		if (!$wrapper) throw HTTP_Exception::factory(404);

		// Database transaction start
		Database::instance()->begin();

		// Try
		try
		{
			/**
			 * Check other tables
			 */
			// used by divisions
			$used_divisions = (bool) Tbl::factory('divisions')
					->where('wrapper_id', '=', $wrapper->id)
					->read()
					->count();

			// If this warpper is used by division
			if ($used_divisions) throw new Warning_Exception(Kohana::message('general', 'wrapper_is_used'));

			/**
			 * Delete
			 */
			// Delete file
			$file = "wrapper/{$wrapper->segment}";
			Cms_Helper::delete_file($file, $this->settings->front_tpl_dir);

			// Delete
			$wrapper->delete();

			// Database commit
			Database::instance()->commit();

			// Add success notice
			Notice::add(Notice::SUCCESS, Kohana::message('general', 'delete_success'));

			// Redirect to wrapper index
			$this->redirect(URL::site("{$this->settings->backend_name}/wrappers/index", 'http'));
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
			Notice::add(Notice::ERROR);
		}

		// Redirect to wrapper edit
		$this->redirect(URL::site("{$this->settings->backend_name}/wrappers/edit/{$wrapper->id}", 'http'));
	}

	/**
	 * After
	 */
	public function after()
	{
		parent::after();
	}

}
