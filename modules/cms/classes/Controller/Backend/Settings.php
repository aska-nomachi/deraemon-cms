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
			'paginate' => array('name' => 'paginate', 'url' => URL::site("{$this->settings->backend_name}/settings/paginate", 'http')),
			'image' => array('name' => 'image', 'url' => URL::site("{$this->settings->backend_name}/settings/image", 'http')),
			'email' => array('name' => 'email', 'url' => URL::site("{$this->settings->backend_name}/settings/email", 'http')),
			'comment' => array('name' => 'comment', 'url' => URL::site("{$this->settings->backend_name}/settings/comment", 'http')),
			'auth' => array('name' => 'auth', 'url' => URL::site("{$this->settings->backend_name}/settings/auth", 'http')),
			'other' => array('name' => 'other', 'url' => URL::site("{$this->settings->backend_name}/settings/other", 'http')),
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

	/**
	 * Frontend
	 */
	public function action_frontend()
	{
		// Additions
		$additions = [
			'front_theme' => [
				'message' => 'テーマフォルダの名前を入力します。',
				'type' => 'text',
				'rule' => ['not_empty' => '', 'alpha_numeric' => ''],
				'label' => 'front theme',
			],
			'lang' => [
				'message' => 'フロントエンドの言語を入力します。',
				'type' => 'text',
				'rule' => ['not_empty' => ''],
				'label' => 'lang',
			],
			'timezoon' => [
				'message' => 'タイムゾーンを設定します。',
				'type' => 'text',
				'rule' => ['not_empty' => ''],
				'label' => 'lang',
			],
			'home_page' => [
				'message' => '最初に表示する、itemのsegmentを入力します。',
				'type' => 'text',
				'rule' => ['not_empty' => ''],
				'label' => 'home page',
			],
			'site_title' => [
				'message' => 'サイトのタイトルを入力します。',
				'type' => 'text',
				'rule' => [],
				'label' => '',
			],
			'site_email_address' => [
				'message' => 'サイトのメールアドレスを入力します。',
				'type' => 'text',
				'rule' => [],
				'label' => '',
			],
			'site_details' => [
				'message' => 'コロン「：」で区切ってキーとバリューを入力、改行で次の項目を追加できます。<br />example)<br />name : sitename<br />phone : 000 0000<br />address : aaa bbb ccc',
				'type' => 'textarea',
				'rule' => [],
				'label' => '',
			]
		];

		// Get settings
		$settings = Tbl::factory('settings')
				->order_by('id')
				->where('key', 'in', array_keys($additions))
				->read()
				->as_array('key');

		foreach ($settings as $key => $val)
		{
			$val->message = Arr::path($additions, $key . '.message');
			$val->type = Arr::path($additions, $key . '.type');
		}
		unset($val);

		// If there are post
		if ($this->request->post())
		{
			$posts = $this->request->post();

			// Filter $settings
			foreach ($posts as $key => $val)
			{
				if (!isset($settings[$key]))
				{
					unset($posts[$key]);
				}
				else
				{
					$settings[$key]->value = $val;
				}
			}
			unset($val);

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				// Validation
				$validation = Validation::factory($posts);

				foreach ($additions as $addition_key => $addition_val)
				{
					foreach ($addition_val['rule'] as $key => $value)
					{
						if ($key)
						{
							if ($value)
							{
								$validation->rule($addition_key, $key, $value);
							}
							else
							{
								$validation->rule($addition_key, $key);
							}
							$validation->label($addition_key, __($addition_val['label']));
						}
					}
				}

				// Check validation
				if (!$validation->check())
				{
					throw new Validation_Exception($validation);
				}

				// Update
				foreach ($posts as $key => $val)
				{
					Tbl::factory('settings')
							->where('key', '=', $key)
							->get()
							->update(array(
								'value' => $val,
					));
				}
				unset($val);

				// Get settings again
				$settings = Tbl::factory('settings')
						->order_by('id')
						->where('key', 'in', array_keys($additions))
						->read()
						->as_array('key');

				foreach ($settings as $key => $val)
				{
					$val->message = Arr::path($additions, $key . '.message');
					$val->type = Arr::path($additions, $key . '.type');
				}
				unset($val);

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
		// Additions
		$additions = [
			'direct_key' => [
				'message' => '初めてログインするユーザの設定につかいます。',
				'type' => 'text',
			],
			'backend_name' => [
				'message' => '管理画面のurlの部分を入力します。',
				'type' => 'text',
			],
			'backend_lang' => [
				'message' => '管理画面で使用する言語を入力します。 現在、「en」、「ja」、「kr」',
				'type' => 'text',
			],
		];

		// Get settings
		$settings = Tbl::factory('settings')
				->order_by('id')
				->where('key', 'in', array_keys($additions))
				->read()
				->as_array('key');

		foreach ($settings as $key => $val)
		{
			$val->message = Arr::path($additions, $key . '.message');
			$val->type = Arr::path($additions, $key . '.type');
		}
		unset($val);

		// If there are post
		if ($this->request->post())
		{
			$posts = $this->request->post();

			// Set post to settings
			foreach ($posts as $key => $val)
			{
				if (!isset($settings[$key]))
				{
					unset($posts[$key]);
				}
				else
				{
					$settings[$key]->value = $val;
				}
			}
			unset($val);

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				$validation = Validation::factory($posts)
						->rule('direct_key', 'min_length', array(':value', '32'))
						->rule('backend_name', 'alpha_numeric')
						->rule('backend_lang', 'not_empty')
						->rule('backend_lang', 'in_array', array(':value', array('en', 'ja', 'kr')))
						->label('direct_key', 'direct key')
						->label('backend_name', 'backend name')
						->label('backend_lang', 'backend lang')
				;

				// Check validation
				if (!$validation->check())
				{
					throw new Validation_Exception($validation);
				}

				// Update
				foreach ($posts as $key => $val)
				{
					Tbl::factory('settings')
							->where('key', '=', $key)
							->get()
							->update(array(
								'value' => $val,
					));
				}
				unset($val);

				// Get settings again
				$settings = Tbl::factory('settings')
						->order_by('id')
						->where('key', 'in', array_keys($additions))
						->read()
						->as_array('key');

				foreach ($settings as $key => $val)
				{
					$val->message = Arr::path($additions, $key . '.message');
					$val->type = Arr::path($additions, $key . '.type');
				}
				unset($val);

				// Database commit
				Database::instance()->commit();

				// Add success notice
				Notice::add(Notice::SUCCESS, Kohana::message('general', 'update_success'));

				// Redirect
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
	 * Paginate
	 */
	public function action_paginate()
	{
		// Additions
		$additions = [
			'paginate_items_per_page_for_items' => [
				'message' => '',
				'type' => 'text',
			],
			'paginate_items_follow_for_items' => [
				'message' => '',
				'type' => 'text',
			],
			'paginate_items_per_page_for_users' => [
				'message' => '',
				'type' => 'text',
			],
			'paginate_items_follow_for_users' => [
				'message' => '',
				'type' => 'text',
			],
			'paginate_items_per_page_for_received_emails' => [
				'message' => '',
				'type' => 'text',
			],
			'paginate_items_follow_for_received_emails' => [
				'message' => '',
				'type' => 'text',
			],
			'paginate_items_per_page_for_received_comments' => [
				'message' => '',
				'type' => 'text',
			],
			'paginate_items_follow_for_received_comments' => [
				'message' => '',
				'type' => 'text',
			],
		];

		// Get settings
		$settings = Tbl::factory('settings')
				->order_by('id')
				->where('key', 'in', array_keys($additions))
				->read()
				->as_array('key');

		foreach ($settings as $key => $val)
		{
			$val->message = Arr::path($additions, $key . '.message');
			$val->type = Arr::path($additions, $key . '.type');
		}
		unset($val);

		// If there are post
		if ($this->request->post())
		{
			$posts = $this->request->post();

			// Set post to settings
			foreach ($posts as $key => $val)
			{
				if (!isset($settings[$key]))
				{
					unset($posts[$key]);
				}
				else
				{
					$settings[$key]->value = $val;
				}
			}
			unset($val);

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				$validation = Validation::factory($posts)
						->rule('paginate_items_per_page_for_items', 'not_empty')
						->rule('paginate_items_per_page_for_items', 'numeric')
						->rule('paginate_items_follow_for_items', 'not_empty')
						->rule('paginate_items_follow_for_items', 'numeric')
						->rule('paginate_items_per_page_for_users', 'not_empty')
						->rule('paginate_items_per_page_for_users', 'numeric')
						->rule('paginate_items_follow_for_users', 'not_empty')
						->rule('paginate_items_follow_for_users', 'numeric')
						->rule('paginate_items_per_page_for_received_emails', 'not_empty')
						->rule('paginate_items_per_page_for_received_emails', 'numeric')
						->rule('paginate_items_follow_for_received_emails', 'not_empty')
						->rule('paginate_items_follow_for_received_emails', 'numeric')
						->rule('paginate_items_per_page_for_received_comments', 'not_empty')
						->rule('paginate_items_per_page_for_received_comments', 'numeric')
						->rule('paginate_items_follow_for_received_comments', 'not_empty')
						->rule('paginate_items_follow_for_received_comments', 'numeric')
						->label('paginate_items_per_page_for_items', 'paginate items_per page for items')
						->label('paginate_items_follow_for_items', 'paginate items follow for items')
						->label('paginate_items_per_page_for_users', 'paginate items per page for users')
						->label('paginate_items_follow_for_users', 'paginate items follow for users')
						->label('paginate_items_per_page_for_received_emails', 'paginate items per page for received emails')
						->label('paginate_items_follow_for_received_emails', 'paginate items follow for received emails')
						->label('paginate_items_per_page_for_received_comments', 'paginate items per page for received comments')
						->label('paginate_items_follow_for_received_comments', 'paginate items follow for received comments')
				;

				// Check validation
				if (!$validation->check())
				{
					throw new Validation_Exception($validation);
				}

				// Update
				foreach ($posts as $key => $val)
				{
					Tbl::factory('settings')
							->where('key', '=', $key)
							->get()
							->update(array(
								'value' => $val,
					));
				}
				unset($val);

				// Get settings again
				$settings = Tbl::factory('settings')
						->order_by('id')
						->where('key', 'in', array_keys($additions))
						->read()
						->as_array('key');

				foreach ($settings as $key => $val)
				{
					$val->message = Arr::path($additions, $key . '.message');
					$val->type = Arr::path($additions, $key . '.type');
				}
				unset($val);

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
		$content_file = Tpl::get_file('paginate', $this->settings->back_tpl_dir . '/settings', $this->partials);

		$this->content = Tpl::factory($content_file)
				->set('settings', $settings);
	}

	/**
	 * Image
	 */
	public function action_image()
	{
		// Additions
		$additions = [
			'image_v' => [
				'message' => '',
				'type' => 'text',
			],
			'image_h' => [
				'message' => '',
				'type' => 'text',
			],
			'image_s' => [
				'message' => '',
				'type' => 'text',
			],
		];

		// Get settings
		$settings = Tbl::factory('settings')
				->order_by('id')
				->where('key', 'in', array_keys($additions))
				->read()
				->as_array('key');

		foreach ($settings as $key => $val)
		{
			$val->message = Arr::path($additions, $key . '.message');
			$val->type = Arr::path($additions, $key . '.type');
		}
		unset($val);

		// If there are post
		if ($this->request->post())
		{
			$posts = $this->request->post();

			// Set post to settings
			foreach ($posts as $key => $val)
			{
				if (!isset($settings[$key]))
				{
					unset($posts[$key]);
				}
				else
				{
					$settings[$key]->value = $val;
				}
			}
			unset($val);

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				$validation = Validation::factory($posts)
						->rule('image_v', 'not_empty')
						->rule('image_v', 'regex', array(':value', '/^([0-9])+(,| )+([0-9])+$/'))
						->rule('image_h', 'not_empty')
						->rule('image_h', 'regex', array(':value', '/^([0-9])+(,| )+([0-9])+$/'))
						->rule('image_s', 'not_empty')
						->rule('image_s', 'regex', array(':value', '/^([0-9])+(,| )+([0-9])+$/'))
						->label('image_v', 'image size vertical')
						->label('image_h', 'image size horizontal')
						->label('image_s', 'image size square')
				;

				// Check validation
				if (!$validation->check())
				{
					throw new Validation_Exception($validation);
				}

				// Update
				foreach ($posts as $key => $val)
				{
					Tbl::factory('settings')
							->where('key', '=', $key)
							->get()
							->update(array(
								'value' => $val,
					));
				}
				unset($val);

				// Get settings again
				$settings = Tbl::factory('settings')
						->order_by('id')
						->where('key', 'in', array_keys($additions))
						->read()
						->as_array('key');

				foreach ($settings as $key => $val)
				{
					$val->message = Arr::path($additions, $key . '.message');
					$val->type = Arr::path($additions, $key . '.type');
				}
				unset($val);

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
		$content_file = Tpl::get_file('image', $this->settings->back_tpl_dir . '/settings', $this->partials);

		$this->content = Tpl::factory($content_file)
				->set('settings', $settings);
	}

	/**
	 * Mail
	 */
	public function action_email()
	{
		// Additions
		$additions = [
			'smtp_hostname' => [
				'message' => '',
				'type' => 'text',
			],
			'smtp_username' => [
				'message' => '',
				'type' => 'text',
			],
			'smtp_password' => [
				'message' => '',
				'type' => 'text',
			],
			'send_email_is_on' => [
				'message' => '',
				'type' => 'checkbox',
			],
			'send_email_save_is_on' => [
				'message' => '',
				'type' => 'checkbox',
			],
			'send_email_confirm_is_on' => [
				'message' => '',
				'type' => 'checkbox',
			],
			'send_email_allowable_tags' => [
				'message' => '',
				'type' => 'text',
			],
			'send_email_defult_receive_subject' => [
				'message' => '',
				'type' => 'text',
			],
			'send_email_defult_user_name' => [
				'message' => '',
				'type' => 'text',
			],
			'send_email_defult_user_address' => [
				'message' => '',
				'type' => 'text',
			],
			'send_email_defult_confirm_subject' => [
				'message' => '',
				'type' => 'text',
			],
			'send_email_defult_user_address' => [
				'message' => '',
				'type' => 'text',
			],
			'send_email_defult_confirm_subject' => [
				'message' => '',
				'type' => 'text',
			],
			'send_email_defult_admin_name' => [
				'message' => '',
				'type' => 'text',
			],
			'send_email_defult_admin_address' => [
				'message' => '',
				'type' => 'text',
			],
		];

		// Get settings
		$settings = Tbl::factory('settings')
				->order_by('id')
				->where('key', 'in', array_keys($additions))
				->read()
				->as_array('key');

		foreach ($settings as $key => $val)
		{
			$val->message = Arr::path($additions, $key . '.message');
			$val->type = Arr::path($additions, $key . '.type');
		}
		unset($val);

		// If there are post
		if ($this->request->post())
		{
			$posts = $this->request->post();

			// Set post to settings
			foreach ($posts as $key => $val)
			{
				if (!isset($settings[$key]))
				{
					unset($posts[$key]);
				}
				else
				{
					$settings[$key]->value = $val;
				}
			}
			unset($val);

			// Set checkbox default
			foreach ($settings as $key => $val)
			{
				$posts[$key] = Arr::get($posts, $key, 0);
			}
			unset($val);

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				$validation = Validation::factory($posts)
						->rule('send_email_is_on', 'in_array', array(':value', array(0, 1)))
						->rule('send_email_save_is_on', 'in_array', array(':value', array(0, 1)))
						->rule('send_email_confirm_is_on', 'in_array', array(':value', array(0, 1)))
						->rule('send_email_defult_user_address', 'email')
						->rule('send_email_defult_admin_address', 'email')
						->label('send_email_is_on', 'send email is on')
						->label('send_email_save_is_on', 'send email save is on')
						->label('send_email_confirm_is_on', 'send email confirm is on')
				;

				// Check validation
				if (!$validation->check())
				{
					throw new Validation_Exception($validation);
				}

				// Update
				foreach ($posts as $key => $val)
				{
					Tbl::factory('settings')
							->where('key', '=', $key)
							->get()
							->update(array(
								'value' => $val,
					));
				}
				unset($val);

				// Get settings again
				$settings = Tbl::factory('settings')
						->order_by('id')
						->where('key', 'in', array_keys($additions))
						->read()
						->as_array('key');

				foreach ($settings as $key => $val)
				{
					$val->message = Arr::path($additions, $key . '.message');
					$val->type = Arr::path($additions, $key . '.type');
				}
				unset($val);

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
		$content_file = Tpl::get_file('email', $this->settings->back_tpl_dir . '/settings', $this->partials);

		$this->content = Tpl::factory($content_file)
				->set('settings', $settings);
	}

	/**
	 * Comment
	 */
	public function action_comment()
	{
		// Additions
		$additions = [
			'send_comment_is_on' => [
				'message' => '',
				'type' => 'checkbox',
			],
			'send_comment_is_user_only' => [
				'message' => '',
				'type' => 'checkbox',
			],
			'send_comment_is_on_default' => [
				'message' => '',
				'type' => 'checkbox',
			],
			'send_comment_is_accept_default' => [
				'message' => '各アイテムごとに設定するコメントの設定のデフォルト値',
				'type' => 'checkbox',
			],
			'send_comment_allowable_tags' => [
				'message' => '',
				'type' => 'text',
			],
		];

		// Get settings
		$settings = Tbl::factory('settings')
				->order_by('id')
				->where('key', 'in', array_keys($additions))
				->read()
				->as_array('key');

		foreach ($settings as $key => $val)
		{
			$val->message = Arr::path($additions, $key . '.message');
			$val->type = Arr::path($additions, $key . '.type');
		}
		unset($val);

		// If there are post
		if ($this->request->post())
		{
			$posts = $this->request->post();

			// Set post to settings
			foreach ($posts as $key => $val)
			{
				if (!isset($settings[$key]))
				{
					unset($posts[$key]);
				}
				else
				{
					$settings[$key]->value = $val;
				}
			}
			unset($val);

			// Set checkbox default
			foreach ($settings as $key => $val)
			{
				$posts[$key] = Arr::get($posts, $key, 0);
			}
			unset($val);

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				$validation = Validation::factory($posts)
						->rule('send_comment_is_on', 'in_array', array(':value', array(0, 1)))
						->rule('send_comment_is_user_only', 'in_array', array(':value', array(0, 1)))
						->rule('send_comment_is_on_default', 'in_array', array(':value', array(0, 1)))
						->rule('send_comment_is_accept_default', 'in_array', array(':value', array(0, 1)))
						->label('send_comment_is_on', 'send email is on')
						->label('send_comment_is_user_only', 'send email save is on')
						->label('send_comment_is_on_default', 'send email confirm is on')
						->label('send_comment_is_accept_default', 'send email confirm is on')
				;

				// Check validation
				if (!$validation->check())
				{
					throw new Validation_Exception($validation);
				}

				// Update
				foreach ($posts as $key => $val)
				{
					Tbl::factory('settings')
							->where('key', '=', $key)
							->get()
							->update(array(
								'value' => $val,
					));
				}
				unset($val);

				// Get settings again
				$settings = Tbl::factory('settings')
						->order_by('id')
						->where('key', 'in', array_keys($additions))
						->read()
						->as_array('key');

				foreach ($settings as $key => $val)
				{
					$val->message = Arr::path($additions, $key . '.message');
					$val->type = Arr::path($additions, $key . '.type');
				}
				unset($val);

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
		$content_file = Tpl::get_file('comment', $this->settings->back_tpl_dir . '/settings', $this->partials);

		$this->content = Tpl::factory($content_file)
				->set('settings', $settings);
	}

	/**
	 * Auth
	 */
	public function action_auth()
	{
		// Additions
		$additions = [
			'author_login_is_on' => [
				'message' => '',
				'type' => 'checkbox',
			],
			'author_register_is_on' => [
				'message' => '',
				'type' => 'checkbox',
			],
			'author_register_activate_is_on' => [
				'message' => '',
				'type' => 'checkbox',
			],
			'author_register_activate_subject' => [
				'message' => '',
				'type' => 'text',
			],
			'author_register_activate_email_type' => [
				'message' => '',
				'type' => 'text',
			],
			'author_register_activate_from_address' => [
				'message' => '',
				'type' => 'text',
			],
			'author_register_activate_from_name' => [
				'message' => '',
				'type' => 'text',
			],
			'author_register_activate_access_key' => [
				'message' => '',
				'type' => 'text',
			],
			'author_register_activate_key_delimiter' => [
				'message' => '',
				'type' => 'text',
			],
			'author_register_default_is_block' => [
				'message' => '',
				'type' => 'checkbox',
			],
			'author_password_forgot_is_on' => [
				'message' => '',
				'type' => 'checkbox',
			],
			'author_password_reset_subject' => [
				'message' => '',
				'type' => 'text',
			],
			'author_password_reset_email_type' => [
				'message' => '',
				'type' => 'text',
			],
			'author_password_reset_from_address' => [
				'message' => '',
				'type' => 'text',
			],
			'author_password_reset_from_name' => [
				'message' => '',
				'type' => 'text',
			],
			'author_password_reset_key_delimiter' => [
				'message' => '',
				'type' => 'text',
			],
			'author_password_is_on' => [
				'message' => '',
				'type' => 'checkbox',
			],
			'author_account_is_on' => [
				'message' => '',
				'type' => 'checkbox',
			],
			'author_detail_is_on' => [
				'message' => '',
				'type' => 'checkbox',
			],
		];

		// Get settings
		$settings = Tbl::factory('settings')
				->order_by('id')
				->where('key', 'in', array_keys($additions))
				->read()
				->as_array('key');

		foreach ($settings as $key => $val)
		{
			$val->message = Arr::path($additions, $key . '.message');
			$val->type = Arr::path($additions, $key . '.type');
		}
		unset($val);

		// If there are post
		if ($this->request->post())
		{
			$posts = $this->request->post();

			// Set post to settings
			foreach ($posts as $key => $val)
			{
				if (!isset($settings[$key]))
				{
					unset($posts[$key]);
				}
				else
				{
					$settings[$key]->value = $val;
				}
			}
			unset($val);

			// Set checkbox default
			foreach ($settings as $key => $val)
			{
				$posts[$key] = Arr::get($posts, $key, 0);
			}
			unset($val);

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				$validation = Validation::factory($posts)
						->rule('author_login_is_on', 'in_array', array(':value', array(0, 1)))
						->rule('author_register_is_on', 'in_array', array(':value', array(0, 1)))
						->rule('author_register_activate_is_on', 'in_array', array(':value', array(0, 1)))
						->rule('author_register_activate_subject', 'not_empty')
						->rule('author_register_activate_email_type', 'not_empty')
						->rule('author_register_activate_from_address', 'not_empty')
						->rule('author_register_activate_from_name', 'not_empty')
						->rule('author_register_activate_access_key', 'not_empty')
						->rule('author_register_activate_key_delimiter', 'not_empty')
						->rule('author_register_default_is_block', 'in_array', array(':value', array(0, 1)))
						->rule('author_password_forgot_is_on', 'in_array', array(':value', array(0, 1)))
						->rule('author_password_reset_subject', 'not_empty')
						->rule('author_password_reset_email_type', 'not_empty')
						->rule('author_password_reset_from_address', 'not_empty')
						->rule('author_password_reset_from_name', 'not_empty')
						->rule('author_password_reset_key_delimiter', 'not_empty')
						->rule('author_password_is_on', 'in_array', array(':value', array(0, 1)))
						->rule('author_account_is_on', 'in_array', array(':value', array(0, 1)))
						->rule('author_detail_is_on', 'in_array', array(':value', array(0, 1)))
						->label('author_login_is_on', 'author login is on')
						->label('author_register_is_on', 'author register is on')
						->label('author_register_activate_is_on', 'author register activate is on')
						->label('author_register_activate_subject', 'author register activate subject')
						->label('author_register_activate_email_type', 'author register activate email type')
						->label('author_register_activate_from_address', 'author register activate from address')
						->label('author_register_activate_from_name', 'author register activate from name')
						->label('author_register_activate_access_key', 'author register activate access key')
						->label('author_register_activate_key_delimiter', 'author register activate key delimiter')
						->label('author_register_default_is_block', 'author register default is block')
						->label('author_password_forgot_is_on', 'author password forgot is on')
						->label('author_password_reset_subject', 'author password reset subject')
						->label('author_password_reset_email_type', 'author password reset email type')
						->label('author_password_reset_from_address', 'author password reset from address')
						->label('author_password_reset_from_name', 'author password reset from name')
						->label('author_password_reset_key_delimiter', 'author password reset key delimiter')
						->label('author_password_is_on', 'author password is on')
						->label('author_account_is_on', 'author account is on')
						->label('author_detail_is_on', 'author detail is on')
				;

				// Check validation
				if (!$validation->check())
				{
					throw new Validation_Exception($validation);
				}

				// Update
				foreach ($posts as $key => $val)
				{
					Tbl::factory('settings')
							->where('key', '=', $key)
							->get()
							->update(array(
								'value' => $val,
					));
				}
				unset($val);

				// Get settings again
				$settings = Tbl::factory('settings')
						->order_by('id')
						->where('key', 'in', array_keys($additions))
						->read()
						->as_array('key');

				foreach ($settings as $key => $val)
				{
					$val->message = Arr::path($additions, $key . '.message');
					$val->type = Arr::path($additions, $key . '.type');
				}
				unset($val);

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
		$content_file = Tpl::get_file('auth', $this->settings->back_tpl_dir . '/settings', $this->partials);

		$this->content = Tpl::factory($content_file)
				->set('settings', $settings);
	}

	/**
	 * Other
	 */
	public function action_other()
	{
		// Additions
		$additions = [
			'temp_pre' => [
				'message' => '',
				'type' => 'text',
			],
			'temp_dir' => [
				'message' => '',
				'type' => 'text',
			],
			'tpl_func' => [
				'message' => '',
				'type' => 'text',
			],
			'item_dir' => [
				'message' => '',
				'type' => 'text',
			],
			'image_dir' => [
				'message' => '',
				'type' => 'text',
			],
			'encrypt_mode' => [
				'message' => '',
				'type' => 'text',
			],
			'encrypt_cipher' => [
				'message' => '',
				'type' => 'text',
			],
			'encrypt_key' => [
				'message' => '',
				'type' => 'text',
			],
			'cooki_salt' => [
				'message' => '',
				'type' => 'text',
			],
			'cooki_expiration' => [
				'message' => '',
				'type' => 'text',
			],
			'session_name' => [
				'message' => '',
				'type' => 'text',
			],
			'deraemon_session' => [
				'message' => '',
				'type' => 'text',
			],
			'auth_hash_key' => [
				'message' => '',
				'type' => 'text',
			],
			'auth_lifetime' => [
				'message' => '',
				'type' => 'text',
			],
			'auth_session_key' => [
				'message' => '',
				'type' => 'text',
			],
		];

		// Get settings
		$settings = Tbl::factory('settings')
				->order_by('id')
				->where('key', 'in', array_keys($additions))
				->read()
				->as_array('key');

		foreach ($settings as $key => $val)
		{
			$val->message = Arr::path($additions, $key . '.message');
			$val->type = Arr::path($additions, $key . '.type');
		}
		unset($val);

		// If there are post
		if ($this->request->post())
		{
			$posts = $this->request->post();

			// Set post to settings
			foreach ($posts as $key => $val)
			{
				if (!isset($settings[$key]))
				{
					unset($posts[$key]);
				}
				else
				{
					$settings[$key]->value = $val;
				}
			}
			unset($val);

			// Set checkbox default
			foreach ($settings as $key => $val)
			{
				$posts[$key] = Arr::get($posts, $key, 0);
			}
			unset($val);

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				$validation = Validation::factory($posts)
						->rule('temp_pre', 'not_empty')
						->rule('temp_dir', 'not_empty')
						->rule('tpl_func', 'not_empty')
						->rule('item_dir', 'not_empty')
						->rule('image_dir', 'not_empty')
						->rule('encrypt_mode', 'not_empty')
						->rule('encrypt_cipher', 'not_empty')
						->rule('encrypt_key', 'not_empty')
						->rule('cooki_salt', 'not_empty')
						->rule('cooki_expiration', 'not_empty')
						->rule('session_name', 'not_empty')
						->rule('deraemon_session', 'not_empty')
						->rule('auth_hash_key', 'not_empty')
						->rule('auth_lifetime', 'not_empty')
						->rule('auth_session_key', 'not_empty')
						->label('temp_pre', 'temp pre')
						->label('temp_dir', 'temp dir')
						->label('tpl_func', 'tpl func')
						->label('item_dir', 'item dir')
						->label('image_dir', 'image dir')
						->label('encrypt_mode', 'encrypt mode')
						->label('encrypt_cipher', 'encrypt cipher')
						->label('encrypt_key', 'encrypt key')
						->label('cooki_salt', 'cooki salt')
						->label('cooki_expiration', 'cooki expiration')
						->label('session_name', 'session name')
						->label('deraemon_session', 'deraemon session')
						->label('auth_hash_key', 'auth hash key')
						->label('auth_lifetime', 'auth lifetime')
						->label('auth_session_key', 'auth session key')
				;

				// Check validation
				if (!$validation->check())
				{
					throw new Validation_Exception($validation);
				}

				// Update
				foreach ($posts as $key => $val)
				{
					Tbl::factory('settings')
							->where('key', '=', $key)
							->get()
							->update(array(
								'value' => $val,
					));
				}
				unset($val);

				// Get settings again
				$settings = Tbl::factory('settings')
						->order_by('id')
						->where('key', 'in', array_keys($additions))
						->read()
						->as_array('key');

				foreach ($settings as $key => $val)
				{
					$val->message = Arr::path($additions, $key . '.message');
					$val->type = Arr::path($additions, $key . '.type');
				}
				unset($val);

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
		$content_file = Tpl::get_file('other', $this->settings->back_tpl_dir . '/settings', $this->partials);

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
