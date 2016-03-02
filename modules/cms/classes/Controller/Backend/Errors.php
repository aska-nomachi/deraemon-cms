<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Errors extends Controller_Backend_Template
{

	public $errors = array();
	public $local_menus = array();

	// Todo::6 500にはいらない。。。なんで？
	/**
	 * Before
	 */
	public function before()
	{
		parent::before();

		// Local menus
		$this->local_menus = array(
			'404' => array('name' => '404', 'url' => URL::site("{$this->settings->backend_name}/errors/404", 'http')),
			'500' => array('name' => '500', 'url' => URL::site("{$this->settings->backend_name}/errors/500", 'http')),
			'default' => array('name' => 'default', 'url' => URL::site("{$this->settings->backend_name}/errors/default", 'http')),
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
		$this->redirect(URL::site("{$this->settings->backend_name}/errors/404", 'http'));
	}

	/**
	 * Action 404
	 */
	public function action_404()
	{
		// Get content from file and direct set to _404
		$_404 = new stdClass();
		$_404->content = Tpl::get_file('404', $this->settings->front_tpl_dir . $this->settings->front_theme . '/error');

		// If there are post
		if ($this->request->post())
		{
			// Set post to author
			$_404->content = $this->request->post('content');

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				// Update file
				Cms_Helper::set_file("404", $this->settings->front_tpl_dir . $this->settings->front_theme . '/error', $this->request->post('content'));

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
		$content_file = Tpl::get_file('404', $this->settings->back_tpl_dir . '/errors', $this->partials);

		$this->content = Tpl::factory($content_file)
				->set('_404', $_404);
	}

	/**
	 * Action 500
	 */
	public function action_500()
	{
		// Get content from file and direct set to _500
		$_500 = new stdClass();
		$_500->content = Tpl::get_file('500', $this->settings->front_tpl_dir . $this->settings->front_theme . '/error');

		// If there are post
		if ($this->request->post())
		{
			// Set post to author
			$_500->content = $this->request->post('content');

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				// Update file
				Cms_Helper::set_file("500", $this->settings->front_tpl_dir . $this->settings->front_theme . '/error', $this->request->post('content'));

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
		$content_file = Tpl::get_file('500', $this->settings->back_tpl_dir . '/errors', $this->partials);

		$this->content = Tpl::factory($content_file)
				->set('_500', $_500);
	}

	/**
	 * Action default
	 */
	public function action_default()
	{
		// Get content from file and direct set to _default
		$_default = new stdClass();
		$_default->content = Tpl::get_file('default', $this->settings->front_tpl_dir . $this->settings->front_theme . '/error');

		// If there are post
		if ($this->request->post())
		{
			// Set post to author
			$_default->content = $this->request->post('content');

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				// Update file
				Cms_Helper::set_file("default", $this->settings->front_tpl_dir . $this->settings->front_theme . '/error', $this->request->post('content'));

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
		$content_file = Tpl::get_file('default', $this->settings->back_tpl_dir . '/errors', $this->partials);

		$this->content = Tpl::factory($content_file)
				->set('_default', $_default);
	}

	/**
	 * After
	 */
	public function after()
	{
		$this->content
				->set('local_menus', $this->local_menus);

		parent::after();
	}

}
