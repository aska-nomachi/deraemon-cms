<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Comment extends Controller_Backend_Template {

	public $email_types = array();

	/**
	 * Before
	 */
	public function before()
	{
		parent::before();

		// Local menus
		$this->local_menus = array(
			'setting' => array('name' => 'setting', 'url' => URL::site("{$this->settings->backend_name}/comment/setting", 'http')),
			'form' => array('name' => 'form', 'url' => URL::site("{$this->settings->backend_name}/comment/form", 'http')),
			'result' => array('name' => 'result', 'url' => URL::site("{$this->settings->backend_name}/comment/result", 'http')),
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
		$this->redirect(URL::site("{$this->settings->backend_name}/comment/setting", 'http'));
	}

	/**
	 * Action setting
	 */
	public function action_setting()
	{
		$settings = new stdClass();
		$settings->send_comment_is_on = $this->settings->send_comment_is_on;
		$settings->send_comment_is_user_only = $this->settings->send_comment_is_user_only;
		$settings->send_comment_is_on_default = $this->settings->send_comment_is_on_default;
		$settings->send_comment_is_accept_default = $this->settings->send_comment_is_accept_default;
		$settings->send_comment_allowable_tags = $this->settings->send_comment_allowable_tags;

		// If there are post
		if ($this->request->post())
		{
			// Set post to email
			$settings->send_comment_is_on = Arr::get($this->request->post(), 'send_comment_is_on', 0);
			$settings->send_comment_is_user_only = Arr::get($this->request->post(), 'send_comment_is_user_only', 0);
			$settings->send_comment_is_on_default = Arr::get($this->request->post(), 'send_comment_is_on_default', 0);
			$settings->send_comment_is_accept_default = Arr::get($this->request->post(), 'send_comment_is_accept_default', 0);
			$settings->send_comment_allowable_tags = Arr::get($this->request->post(), 'send_comment_allowable_tags');

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				foreach ($settings as $key => $value)
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
		$content_file = Tpl::get_file('setting', $this->settings->back_tpl_dir.'/comment', $this->partials);

		$this->content = Tpl::factory($content_file)
			->set('settings', $settings);
	}

	/**
	 * Action form
	 */
	public function action_form()
	{
		// Get form from file and direct set to comment
		$form = new stdClass();
		$form->content = Tpl::get_file('form', $this->settings->front_tpl_dir.'/comment');

		// If there are post
		if ($this->request->post())
		{
			// Set post to author
			$form->content = $this->request->post('content');

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				// Update file
				Cms_Helper::set_file("form", $this->settings->front_tpl_dir.'/comment', $this->request->post('content'));

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
		$content_file = Tpl::get_file('form', $this->settings->back_tpl_dir.'/comment', $this->partials);

		$this->content = Tpl::factory($content_file)
			->set('form', $form);
	}

	/**
	 * Action form
	 */
	public function action_result()
	{
		// Get result file and direct set to comment
		$result = new stdClass();
		$result->content = Tpl::get_file('result', $this->settings->front_tpl_dir.'/comment');

		// If there are post
		if ($this->request->post())
		{
			// Set post to author
			$result->content = $this->request->post('content');

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				// Update file
				Cms_Helper::set_file("result", $this->settings->front_tpl_dir.'/comment', $this->request->post('content'));

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
		$content_file = Tpl::get_file('result', $this->settings->back_tpl_dir.'/comment', $this->partials);

		$this->content = Tpl::factory($content_file)
			->set('result', $result);
	}

	/**
	 * After
	 */
	public function after()
	{
		parent::after();
	}

}
