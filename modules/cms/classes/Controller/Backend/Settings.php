<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Settings extends Controller_Backend_Template {

	public $email_types = array();

	/**
	 * Before
	 */
	public function before()
	{
		parent::before();

		// Local menus
		$this->local_menus = array(
			'index' => array('name' => 'index', 'url' => URL::site("{$this->settings->backend_name}/settings", 'http')),
			'frontend' => array('name' => 'frontend', 'url' => URL::site("{$this->settings->backend_name}/settings/frontend", 'http')),
			'backend' => array('name' => 'backend', 'url' => URL::site("{$this->settings->backend_name}/settings/backend", 'http')),
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

		// email types
		$this->email_types = array(
			array('value' => 'text/plain'),
			array('value' => 'text/html'),
		);
	}

	/**
	 * Action index
	 */
	public function action_index()
	{
		// Get settings
		$settings = Tbl::factory('settings')
				->order_by('id')
				->read()
				->as_array('key');

		// If there are post
		if ($this->request->post())
		{
			// Set post to settings
			foreach ($this->request->post() as $key => $value)
			{
				if (isset($settings[$key]))
					$settings[$key]->value = $value;
			}

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				// Update
				foreach ($this->request->post() as $key => $value)
				{
					Tbl::factory('settings')
							->where('key', '=', $key)
							->get()
							->update(array(
								'value' => $value,
					));
				}

				// Database commit
				Database::instance()->commit();

				// Clear post
				$this->request->post(array());

				// Add success notice
				Notice::add(Notice::SUCCESS, Kohana::message('general', 'create_success'));

				// Redirect バックエンドネームが変わってる時があるから
				$backend_name = Cms_Helper::settings('backend_name');
				$this->redirect(URL::site("{$backend_name}/settings", 'http'));
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

		/**
		 * View
		 */
		// Get content file
		$content_file = Tpl::get_file('index', $this->settings->back_tpl_dir . '/settings', $this->partials);

		$this->content = Tpl::factory($content_file)
				->set('settings', $settings);
	}

	// Todo::3 これ以降はvalidationをつける！
	// 最後にindexに入るとき警告！かな？
	/**
	 * Frontend
	 */
	public function action_frontend()
	{
		$settings = array(
			'frontend_theme' => basename($this->settings->front_tpl_dir),
			'lang' => $this->settings->lang,
			'home_page' => $this->settings->home_page,
			'site_details' => $this->settings->site_details,
		);

		// If there are post
		if ($this->request->post())
		{
			// Set post to email
			$settings['frontend_theme'] = Arr::get($this->request->post(), 'frontend_theme');
			$settings['lang'] = Arr::get($this->request->post(), 'lang');
			$settings['home_page'] = Arr::get($this->request->post(), 'home_page');
			$settings['site_details'] = Arr::get($this->request->post(), 'site_details');

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				$validation = Validation::factory($settings)
						->rule('frontend_theme', 'not_empty')
						->rule('frontend_theme', 'alpha_numeric')
						->rule('lang', 'not_empty')
						->rule('home_page', 'not_empty')
						->label('front_theme', 'Front theme')
						->label('lang', 'Lang')
						->label('home_page', 'Home page')
				;

				// Check validation
				if (!$validation->check())
				{
					throw new Validation_Exception($validation);
				}

				// Build frontend data
				$frontend_data = array(
					'front_tpl_dir' => 'contents/frontend/' . Arr::get($settings, 'frontend_theme'),
					'lang' => Arr::get($settings, 'lang'),
					'home_page' => Arr::get($settings, 'home_page'),
					'site_details' => Arr::get($settings, 'site_details'),
				);

				foreach ($frontend_data as $key => $value)
				{
					Tbl::factory('settings')
							->where('key', '=', $key)
							->get()
							->update(array(
								'value' => $value,
					));
				}

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
		// Get content file
		$content_file = Tpl::get_file('frontend', $this->settings->back_tpl_dir . '/settings', $this->partials);

		$this->content = Tpl::factory($content_file)
				->set('settings', $settings);
	}

	/**
	 * Backend
	 */
	public function action_backend()
	{
		$settings = array(
			'frontend_theme' => basename($this->settings->front_tpl_dir),
			'lang' => $this->settings->lang,
			'home_page' => $this->settings->home_page,
			'site_details' => $this->settings->site_details,
		);

		// If there are post
		if ($this->request->post())
		{
			// Set post to email
			$settings['frontend_theme'] = Arr::get($this->request->post(), 'frontend_theme');
			$settings['lang'] = Arr::get($this->request->post(), 'lang');
			$settings['home_page'] = Arr::get($this->request->post(), 'home_page');
			$settings['site_details'] = Arr::get($this->request->post(), 'site_details');

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				$validation = Validation::factory($settings)
						->rule('frontend_theme', 'not_empty')
						->rule('frontend_theme', 'alpha_numeric')
						->rule('lang', 'not_empty')
						->rule('home_page', 'not_empty')
						->label('front_theme', 'Front theme')
						->label('lang', 'Lang')
						->label('home_page', 'Home page')
				;

				// Check validation
				if (!$validation->check())
				{
					throw new Validation_Exception($validation);
				}

				// Build frontend data
				$frontend_data = array(
					'front_tpl_dir' => 'contents/frontend/' . Arr::get($settings, 'frontend_theme'),
					'lang' => Arr::get($settings, 'lang'),
					'home_page' => Arr::get($settings, 'home_page'),
					'site_details' => Arr::get($settings, 'site_details'),
				);

				foreach ($frontend_data as $key => $value)
				{
					Tbl::factory('settings')
							->where('key', '=', $key)
							->get()
							->update(array(
								'value' => $value,
					));
				}

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
		// Get content file
		$content_file = Tpl::get_file('backend', $this->settings->back_tpl_dir . '/settings', $this->partials);

		$this->content = Tpl::factory($content_file)
				->set('settings', $settings);
	}

	/**
	 * After
	 */
	public function after()
	{
		parent::after();
	}

}
