<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Author extends Controller_Backend_Template {

	public $local_menus = array();
	public $email_types = array();

	// 次backend email！
	// の次form.jsのマッシュアップとvalueの見直し！
	// の次user details
	/**
	 * Before
	 */
	public function before()
	{
		parent::before();

		// Local menus
		$this->local_menus = array(
			'login' => array('name' => 'login', 'url' => URL::site("{$this->settings->backend_name}/author/login", 'http')),
			'register' => array('name' => 'register', 'url' => URL::site("{$this->settings->backend_name}/author/register", 'http')),
			'activate_mail' => array('name' => 'activate mail', 'url' => URL::site("{$this->settings->backend_name}/author/activate_mail", 'http')),
			'activate' => array('name' => 'activate', 'url' => URL::site("{$this->settings->backend_name}/author/activate", 'http')),
			'forgot' => array('name' => 'forgot', 'url' => URL::site("{$this->settings->backend_name}/author/forgot", 'http')),
			'reset_mail' => array('name' => 'reset mail', 'url' => URL::site("{$this->settings->backend_name}/author/reset_mail", 'http')),
			'reset' => array('name' => 'reset', 'url' => URL::site("{$this->settings->backend_name}/author/reset", 'http')),
			'resign' => array('name' => 'resign', 'url' => URL::site("{$this->settings->backend_name}/author/resign", 'http')),
			'account' => array('name' => 'account', 'url' => URL::site("{$this->settings->backend_name}/author/account", 'http')),
			'password' => array('name' => 'password', 'url' => URL::site("{$this->settings->backend_name}/author/password", 'http')),
			'detail' => array('name' => 'detail', 'url' => URL::site("{$this->settings->backend_name}/author/detail", 'http')),
		);

		// email types
		$this->email_types = array(
			array('value' => 'text/plain'),
			array('value' => 'text/html'),
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
		$this->redirect(URL::site("{$this->settings->backend_name}/author/login", 'http'));
	}

	/**
	 * Action login
	 */
	public function action_login()
	{
		// Get content from file and direct set to login
		$login = new stdClass();
		$login->content = Tpl::get_file('login', $this->settings->front_tpl_dir . $this->settings->front_theme . '/author');

		// If there are post
		if ($this->request->post())
		{
			// Set post to author
			$login->content = $this->request->post('content');

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				// Update file
				Cms_Helper::set_file("author/login", $this->settings->front_tpl_dir . $this->settings->front_theme, $this->request->post('content'));

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
		$content_file = Tpl::get_file('login', $this->settings->back_tpl_dir . '/author', $this->partials);

		$this->content = Tpl::factory($content_file)
				->set('login', $login);
	}

	/**
	 * Action register
	 */
	public function action_register()
	{
		// Get content from file and direct set to register
		$register = new stdClass();
		$register->content = Tpl::get_file('register', $this->settings->front_tpl_dir . $this->settings->front_theme . '/author');

		// If there are post
		if ($this->request->post())
		{
			// Set post to author
			$register->content = $this->request->post('content');

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				// Update file
				Cms_Helper::set_file("author/register", $this->settings->front_tpl_dir . $this->settings->front_theme, $this->request->post('content'));

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
		$content_file = Tpl::get_file('register', $this->settings->back_tpl_dir . '/author', $this->partials);

		$this->content = Tpl::factory($content_file)
				->set('register', $register);
	}

	/**
	 * Action activate_mail
	 */
	public function action_activate_mail()
	{
		$activate_settings = new stdClass();
		$activate_settings->author_register_activate_subject = $this->settings->author_register_activate_subject;
		$activate_settings->author_register_activate_email_type = $this->settings->author_register_activate_email_type;
		$activate_settings->author_register_activate_from_address = $this->settings->author_register_activate_from_address;
		$activate_settings->author_register_activate_from_name = $this->settings->author_register_activate_from_name;

		$activate_mail = new stdClass();
		$activate_mail->content = Tpl::get_file('activate_mail', $this->settings->front_tpl_dir . $this->settings->front_theme . '/author');

		// If there are post
		if ($this->request->post())
		{
			// Set post to activate settings
			$activate_settings->author_register_activate_subject = $this->request->post('author_register_activate_subject');
			$activate_settings->author_register_activate_email_type = $this->request->post('author_register_activate_email_type');
			$activate_settings->author_register_activate_from_address = $this->request->post('author_register_activate_from_address');
			$activate_settings->author_register_activate_from_name = $this->request->post('author_register_activate_from_name');
			$activate_mail->content = $this->request->post('content');

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				// Update activate settings
				foreach ($activate_settings as $key => $value)
				{
					Tbl::factory('settings')
							->where('key', '=', $key)
							->get()
							->update(array(
								'value' => $value,
					));
				}

				// Update activate_mail file
				Cms_Helper::set_file("activate_mail", $this->settings->front_tpl_dir . $this->settings->front_theme . '/author', $activate_mail->content);

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
		$content_file = Tpl::get_file('activate_mail', $this->settings->back_tpl_dir . '/author', $this->partials);

		$this->content = Tpl::factory($content_file)
				->set('activate_settings', $activate_settings)
				->set('activate_mail', $activate_mail)
				->set('email_types', $this->email_types);
	}

	/**
	 * Action activate
	 */
	public function action_activate()
	{
		// Get content from file and direct set to activate
		$activate = new stdClass();
		$activate->content = Tpl::get_file('activate', $this->settings->front_tpl_dir . $this->settings->front_theme . '/author');

		// If there are post
		if ($this->request->post())
		{
			// Set post to author
			$activate->content = $this->request->post('content');

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				// Update file
				Cms_Helper::set_file('activate', $this->settings->front_tpl_dir . $this->settings->front_theme . '/author', $this->request->post('content'));

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
		$content_file = Tpl::get_file('activate', $this->settings->back_tpl_dir . '/author', $this->partials);

		$this->content = Tpl::factory($content_file)
				->set('activate', $activate);
	}

	/**
	 * Action forgot
	 */
	public function action_forgot()
	{
		// Get content from file and direct set to forgot
		$forgot = new stdClass();
		$forgot->content = Tpl::get_file('forgot', $this->settings->front_tpl_dir . $this->settings->front_theme . '/author');

		// If there are post
		if ($this->request->post())
		{
			// Set post to author
			$forgot->content = $this->request->post('content');

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				// Update file
				Cms_Helper::set_file('forgot', $this->settings->front_tpl_dir . $this->settings->front_theme . '/author', $this->request->post('content'));

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
		$content_file = Tpl::get_file('forgot', $this->settings->back_tpl_dir . '/author', $this->partials);

		$this->content = Tpl::factory($content_file)
				->set('forgot', $forgot);
	}

	/**
	 * Action reset mail
	 */
	public function action_reset_mail()
	{
		$reset_settings = new stdClass();
		$reset_settings->author_password_forgot_is_on = $this->settings->author_password_forgot_is_on;
		$reset_settings->author_password_reset_subject = $this->settings->author_password_reset_subject;
		$reset_settings->author_password_reset_email_type = $this->settings->author_password_reset_email_type;
		$reset_settings->author_password_reset_from_address = $this->settings->author_password_reset_from_address;
		$reset_settings->author_password_reset_from_name = $this->settings->author_password_reset_from_name;

		$reset_mail = new stdClass();
		$reset_mail->content = Tpl::get_file('reset_mail', $this->settings->front_tpl_dir . $this->settings->front_theme . '/author');

		// If there are post
		if ($this->request->post())
		{
			// Set post to email
			$reset_settings->author_password_forgot_is_on = Arr::get($this->request->post(), 'author_password_forgot_is_on', 0);
			$reset_settings->author_password_reset_subject = Arr::get($this->request->post(), 'author_password_reset_subject');
			$reset_settings->author_password_reset_email_type = $this->request->post('author_password_reset_email_type');
			$reset_settings->author_password_reset_from_address = $this->request->post('author_password_reset_from_address');
			$reset_settings->author_password_reset_from_name = $this->request->post('author_password_reset_from_name');
			$reset_mail->content = $this->request->post('content');

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				foreach ($reset_settings as $key => $value)
				{
					Tbl::factory('settings')
							->where('key', '=', $key)
							->get()
							->update(array(
								'value' => $value,
					));
				}

				// Update reset file
				Cms_Helper::set_file("reset_mail", $this->settings->front_tpl_dir . $this->settings->front_theme . '/author', $reset_mail->content);

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
		$content_file = Tpl::get_file('reset_mail', $this->settings->back_tpl_dir . '/author', $this->partials);

		$this->content = Tpl::factory($content_file)
				->set('reset_settings', $reset_settings)
				->set('reset_mail', $reset_mail)
				->set('email_types', $this->email_types);
	}

	/**
	 * Action reset
	 */
	public function action_reset()
	{
		// Get content from file and direct set to reset
		$reset = new stdClass();
		$reset->content = Tpl::get_file('reset', $this->settings->front_tpl_dir . $this->settings->front_theme . '/author');

		// If there are post
		if ($this->request->post())
		{
			// Set post to author
			$reset->content = $this->request->post('content');

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				// Update file
				Cms_Helper::set_file('reset', $this->settings->front_tpl_dir . $this->settings->front_theme . '/author', $this->request->post('content'));

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
		$content_file = Tpl::get_file('reset', $this->settings->back_tpl_dir . '/author', $this->partials);

		$this->content = Tpl::factory($content_file)
				->set('reset', $reset);
	}

	/**
	 * Action resign
	 */
	public function action_resign()
	{
		// Get content from file and direct set to resign
		$resign = new stdClass();
		$resign->content = Tpl::get_file('resign', $this->settings->front_tpl_dir . $this->settings->front_theme . '/author');

		// If there are post
		if ($this->request->post())
		{
			// Set post to author
			$resign->content = $this->request->post('content');

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				// Update file
				Cms_Helper::set_file('resign', $this->settings->front_tpl_dir . $this->settings->front_theme . '/author', $this->request->post('content'));

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
		$content_file = Tpl::get_file('resign', $this->settings->back_tpl_dir . '/author', $this->partials);

		$this->content = Tpl::factory($content_file)
				->set('resign', $resign);
	}

	/**
	 * Action password
	 */
	public function action_password()
	{
		// Get content from file and direct set to password
		$password = new stdClass();
		$password->content = Tpl::get_file('password', $this->settings->front_tpl_dir . $this->settings->front_theme . '/author');

		// If there are post
		if ($this->request->post())
		{
			// Set post to author
			$password->content = $this->request->post('content');

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				// Update file
				Cms_Helper::set_file('password', $this->settings->front_tpl_dir . $this->settings->front_theme . '/author', $this->request->post('content'));

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
		$content_file = Tpl::get_file('password', $this->settings->back_tpl_dir . '/author', $this->partials);

		$this->content = Tpl::factory($content_file)
				->set('password', $password);
	}

	/**
	 * Action account
	 */
	public function action_account()
	{
		// Get content from file and direct set to account
		$account = new stdClass();
		$account->content = Tpl::get_file('account', $this->settings->front_tpl_dir . $this->settings->front_theme . '/author');

		// If there are post
		if ($this->request->post())
		{
			// Set post to author
			$account->content = $this->request->post('content');

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				// Update file
				Cms_Helper::set_file('account', $this->settings->front_tpl_dir . $this->settings->front_theme . '/author', $this->request->post('content'));

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
		$content_file = Tpl::get_file('account', $this->settings->back_tpl_dir . '/author', $this->partials);

		$this->content = Tpl::factory($content_file)
				->set('account', $account);
	}

	/**
	 * Action detail
	 */
	public function action_detail()
	{
		// Get content from file and direct set to detail
		$detail = new stdClass();
		$detail->content = Tpl::get_file('detail', $this->settings->front_tpl_dir . $this->settings->front_theme . '/author');

		// If there are post
		if ($this->request->post())
		{
			// Set post to author
			$detail->content = $this->request->post('content');

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				// Update file
				Cms_Helper::set_file('detail', $this->settings->front_tpl_dir . $this->settings->front_theme . '/author', $this->request->post('content'));

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

		// usable details
		$usable_details = Tbl::factory('details')
				->read()
				->as_array('segment');

		/**
		 * View
		 */
		$content_file = Tpl::get_file('detail', $this->settings->back_tpl_dir . '/author', $this->partials);

		$this->content = Tpl::factory($content_file)
				->set('usable_details', $usable_details)
				->set('detail', $detail);
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
