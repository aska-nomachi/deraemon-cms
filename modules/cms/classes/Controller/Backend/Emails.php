<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Emails extends Controller_Backend_Template {

	public $email_types = array();

	/**
	 * Before
	 */
	public function before()
	{
		parent::before();

		// Local menus
		$this->local_menus = array(
			'index' => array('name' => 'index', 'url' => URL::site("{$this->settings->backend_name}/emails", 'http')),
			'edit' => array('name' => 'edit', 'url' => URL::site("{$this->settings->backend_name}/emails/edit/{$this->request->param('key')}", 'http')),
			'receive' => array('name' => 'receive', 'url' => URL::site("{$this->settings->backend_name}/emails/receive/{$this->request->param('key')}", 'http')),
			'confirm' => array('name' => 'confirm', 'url' => URL::site("{$this->settings->backend_name}/emails/confirm/{$this->request->param('key')}", 'http')),
			'rule' => array('name' => 'rule', 'url' => URL::site("{$this->settings->backend_name}/emails/rule/{$this->request->param('key')}", 'http')),
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
		// If there are post
		if ($this->request->post())
		{
			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				// Create
				$data = $this->request->post();
				$data['receive_subject'] = $this->settings->send_email_defult_receive_subject;
				$data['receive_email_type'] = 'text/plain';

				$data['confirm_subject'] = $this->settings->send_email_defult_confirm_subject;
				$data['confirm_email_type'] = 'text/plain';
				$data['admin_name'] = $this->settings->send_email_defult_admin_name;
				$data['admin_address'] = $this->settings->send_email_defult_admin_address;
				$email = Tbl::factory('emails')
						->create($data);

				// Create email fiel
				$email_content = '<form method="POST" action="">
	<input type="text" name="name" value="{{send_email_result.post.name}}" />
	<input type="text" name="email" value="{{send_email_result.post.email}}" />
	<input type="hidden" name="[email segment]" value="content">
	<button type="submit" name="send_email" value="1">send</button>
</form>';
				Cms_Helper::set_file($email->segment, $this->settings->front_tpl_dir . $this->settings->front_theme . '/email', $email_content);

				// Create email receive file
				$receive_content = '{{post.type}}メールが届きました。{{return}}
---------------------------------------------------------{{return}}
{{return}}
title：				{{title}}{{return}}
name：				{{name}}{{return}}
email：				{{email}}{{return}}
content：{{return}}
{{content}}{{return}}
{{return}}
---------------------------------------------------------';
				Cms_Helper::set_file("email/receive/{$email->segment}", $this->settings->front_tpl_dir . $this->settings->front_theme, $receive_content);

				// Create email confirm file
				$confirm_content = '以下の内容でメールを受け取りました。{{return}}
---------------------------------------------------------{{return}}
{{return}}
title：				{{title}}{{return}}
name：				{{name}}{{return}}
email：				{{email}}{{return}}
content：{{return}}
{{content}}{{return}}
{{return}}
---------------------------------------------------------';
				Cms_Helper::set_file("email/confirm/{$email->segment}", $this->settings->front_tpl_dir . $this->settings->front_theme, $confirm_content);

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
						Notice::ERROR, $e->getMessage()
				);
			}
		}

		// Get emails
		$emails = Tbl::factory('emails')
				->read()
				->as_array();

		foreach ($emails as $email)
		{
			$email->edit_url = URL::site("{$this->settings->backend_name}/emails/edit/{$email->id}", 'http');
		}

		// local_menusの修正
		$this->local_menus = array(
			$this->local_menus['index']
		);

		/**
		 * View
		 */
		$content_file = Tpl::get_file('index', $this->settings->back_tpl_dir . '/emails', $this->partials);

		$this->content = Tpl::factory($content_file)
				->set('emails', $emails)
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
			throw HTTP_Exception::factory(404);

		// Get email, if there is nothing then throw to 404
		$email = Tbl::factory('emails')->get($id);
		if (!$email)
			throw HTTP_Exception::factory(404);

		// Get content from file and direct set to email
		$email->content = Tpl::get_file($email->segment, $this->settings->front_tpl_dir . $this->settings->front_theme . '/email');
		$email->delete_url = URL::site("{$this->settings->backend_name}/emails/delete/{$email->id}", 'http');

		// Save old name
		$oldname = $email->segment;

		// If there are post
		if ($this->request->post())
		{
			// Set post to email
			$email->segment = $this->request->post('segment');
			$email->name = $this->request->post('name');
			$email->description = $this->request->post('description');
			$email->admin_name = $this->request->post('admin_name');
			$email->admin_address = $this->request->post('admin_address');
			$email->receive_subject = $this->request->post('receive_subject');
			$email->user_name_field = $this->request->post('user_name_field');
			$email->user_address_field = $this->request->post('user_address_field');
			$email->content = $this->request->post('content');

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				// Update
				Tbl::factory('emails')
						->get($email->id)
						->update(array(
							'segment' => $this->request->post('segment'),
							'name' => $this->request->post('name'),
							'description' => $this->request->post('description'),
							'admin_name' => $this->request->post('admin_name'),
							'admin_address' => $this->request->post('admin_address'),
							'receive_subject' => $this->request->post('receive_subject'),
							'user_name_field' => $this->request->post('user_name_field'),
							'user_address_field' => $this->request->post('user_address_field'),
				));

				// New name
				$newname = $email->segment;

				// rename email file
				Cms_Helper::rename_file($oldname, $newname, $this->settings->front_tpl_dir . $this->settings->front_theme . '/email');
				Cms_Helper::rename_file($oldname, $newname, $this->settings->front_tpl_dir . $this->settings->front_theme . '/email/confirm');
				Cms_Helper::rename_file($oldname, $newname, $this->settings->front_tpl_dir . $this->settings->front_theme . '/email/receive');

				// Update file
				Cms_Helper::set_file($newname, $this->settings->front_tpl_dir . $this->settings->front_theme . '/email', $this->request->post('content'));

				// Database commit
				Database::instance()->commit();

				// Add success notice
				Notice::add(Notice::SUCCESS, Kohana::message('general', 'update_success'));

				// Redirect
				$this->redirect(URL::site("{$this->settings->backend_name}/emails/edit/{$email->id}", 'http'));
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
		$this->partials['local_menu'] = Tpl::get_file('local_menu', $this->settings->back_tpl_dir);

		$content_file = Tpl::get_file('edit', $this->settings->back_tpl_dir . '/emails', $this->partials);

		$this->content = Tpl::factory($content_file)
				->set('local_menus', $this->local_menus)
				->set('email', $email);
	}

	/**
	 * Action receive
	 */
	public function action_receive()
	{
		// Get id from param, if there is nothing then throw to 404
		$id = $this->request->param('key');
		if (!$id)
			throw HTTP_Exception::factory(404);

		// Get email, if there is nothing then throw to 404
		$email = Tbl::factory('emails')->get($id);
		if (!$email)
			throw HTTP_Exception::factory(404);

		// Get content from file and direct set to email
		$email->receive_content = Tpl::get_file($email->segment, $this->settings->front_tpl_dir . $this->settings->front_theme . '/email/receive');

		// If there are post
		if ($this->request->post())
		{
			// Set post to email
			$email->receive_subject = $this->request->post('receive_subject');
			$email->receive_email_type = $this->request->post('receive_email_type');
			$email->receive_content = $this->request->post('receive_content');

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				// Update
				Tbl::factory('emails')
						->get($email->id)
						->update(array(
							'receive_subject' => $this->request->post('receive_subject'),
							'receive_email_type' => $this->request->post('receive_email_type'),
				));

				// Update file
				Cms_Helper::set_file($email->segment, $this->settings->front_tpl_dir . $this->settings->front_theme . '/email/receive', $this->request->post('receive_content'));

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
		$this->partials['local_menu'] = Tpl::get_file('local_menu', $this->settings->back_tpl_dir);

		$content_file = Tpl::get_file('receive', $this->settings->back_tpl_dir . '/emails', $this->partials);

		$this->content = Tpl::factory($content_file)
				->set('local_menus', $this->local_menus)
				->set('email_types', $this->email_types)
				->set('email', $email);
	}

	/**
	 * Action confirm
	 */
	public function action_confirm()
	{
		// Get id from param, if there is nothing then throw to 404
		$id = $this->request->param('key');
		if (!$id)
			throw HTTP_Exception::factory(404);

		// Get email, if there is nothing then throw to 404
		$email = Tbl::factory('emails')->get($id);
		if (!$email)
			throw HTTP_Exception::factory(404);

		// Get content from file and direct set to email
		$email->confirm_content = Tpl::get_file($email->segment, $this->settings->front_tpl_dir . $this->settings->front_theme . '/email/confirm');

		// If there are post
		if ($this->request->post())
		{
			// Set post to email
			$email->confirm_subject = $this->request->post('confirm_subject');
			$email->confirm_email_type = $this->request->post('confirm_email_type');
			$email->admin_name = $this->request->post('admin_name');
			$email->admin_address = $this->request->post('admin_address');
			$email->confirm_content = $this->request->post('confirm_content');

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				// Update
				Tbl::factory('emails')
						->get($email->id)
						->update(array(
							'confirm_subject' => $this->request->post('confirm_subject'),
							'confirm_email_type' => $this->request->post('confirm_email_type'),
							'admin_name' => $this->request->post('admin_name'),
							'admin_address' => $this->request->post('admin_address'),
				));

				// Update file
				Cms_Helper::set_file($email->segment, $this->settings->front_tpl_dir . $this->settings->front_theme . '/email/confirm', $this->request->post('confirm_content'));

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
		$this->partials['local_menu'] = Tpl::get_file('local_menu', $this->settings->back_tpl_dir);

		$content_file = Tpl::get_file('confirm', $this->settings->back_tpl_dir . '/emails', $this->partials);

		$this->content = Tpl::factory($content_file)
				->set('local_menus', $this->local_menus)
				->set('email_types', $this->email_types)
				->set('email', $email);
	}

	/**
	 * Action rule
	 */
	public function action_rule()
	{
		// Get id from param, if there is nothing then throw to 404
		$id = $this->request->param('key');
		if (!$id)
			throw HTTP_Exception::factory(404);

		// Get email, if there is nothing then throw to 404
		$email = Tbl::factory('emails')->get($id);
		if (!$email)
			throw HTTP_Exception::factory(404);

		// Get content from file and direct set to email
		$email->edit_url = URL::site("{$this->settings->backend_name}/emails/edit/{$email->id}", 'http');
		$email->confirm_url = URL::site("{$this->settings->backend_name}/emails/confirm/{$email->id}", 'http');
		$email->receive_url = URL::site("{$this->settings->backend_name}/emails/receive/{$email->id}", 'http');
		$email->rule_url = NULL;

		$vaids = array(
			array('callback' => 'not_empty', 'param' => ''),
			array('callback' => 'regex', 'param' => 'regular expression'),
			array('callback' => 'min_length', 'param' => 'minimum number'),
			array('callback' => 'max_length', 'param' => 'maximum number'),
			array('callback' => 'exact_length', 'param' => 'exact number'),
			array('callback' => 'email', 'param' => ''),
			array('callback' => 'email_domain', 'param' => ''),
			array('callback' => 'url', 'param' => '-'),
			array('callback' => 'ip', 'param' => ''),
			array('callback' => 'phone', 'param' => ''),
			array('callback' => 'credit_card', 'param' => ''),
			array('callback' => 'date', 'param' => ''),
			array('callback' => 'alpha', 'param' => ''),
			array('callback' => 'alpha_dash', 'param' => ''),
			array('callback' => 'alpha_numeric', 'param' => ''),
			array('callback' => 'digit', 'param' => ''),
			array('callback' => 'decimal', 'param' => ''),
			array('callback' => 'numeric', 'param' => ''),
			array('callback' => 'range', 'param' => 'min number and max number'),
			array('callback' => 'matches', 'param' => ':value, :field, :mache field'),
			array('callback' => 'equals', 'param' => 'exactly the value required'),
		);

		$create = array();

		// If there are post
		if ($this->request->post('create'))
		{
			// Build data
			$create['email_id'] = $email->id;
			$create['field'] = $this->request->post('create_field');
			$create['callback'] = $this->request->post('create_callback');
			$create['param'] = $this->request->post('create_param');
			$create['label'] = $this->request->post('create_label');

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				// Create
				Tbl::factory('email_rules')
						->create($create);

				// Database commit
				Database::instance()->commit();

				// Clear create
				$create['field'] = NULL;
				$create['callback'] = NULL;
				$create['param'] = NULL;
				$create['label'] = NULL;

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
						Notice::ERROR, $e->getMessage()
				);
			}
		}

		// Get email rules
		$rules = Tbl::factory('email_rules')
				->where('email_id', '=', $email->id)
				->read()
				->as_array();

		foreach ($rules as $rule)
		{
			$rule->delete_url = URL::site("{$this->settings->backend_name}/emails/rule_delete/{$email->id}_{$rule->id}", 'http');
		}

		// If there are post update
		if ($this->request->post('update'))
		{
			$post = $this->request->post();

			// Set post to tag
			foreach ($rules as $rule)
			{
				$rule->field = $post['field'][$rule->id];
				$rule->callback = $post['callback'][$rule->id];
				$rule->param = $post['param'][$rule->id];
				$rule->label = $post['label'][$rule->id];
			}

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				// Update
				foreach ($rules as $rule)
				{
					Tbl::factory('email_rules')
							->get($rule->id)
							->update(array(
								'field' => $post['field'][$rule->id],
								'callback' => $post['callback'][$rule->id],
								'param' => $post['param'][$rule->id],
								'label' => $post['label'][$rule->id],
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
				Notice::add(Notice::VALIDATION, Kohana::message('general', 'update_success'), NULL, $e->errors('validation'));
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
		$this->partials['local_menu'] = Tpl::get_file('local_menu', $this->settings->back_tpl_dir);

		$content_file = Tpl::get_file('rule', $this->settings->back_tpl_dir . '/emails', $this->partials);

		$this->content = Tpl::factory($content_file)
				->set('vaids', $vaids)
				->set('local_menus', $this->local_menus)
				->set('email', $email)
				->set('rules', $rules)
				->set('create', $create);
	}

	/**
	 * Action rule delete
	 */
	public function action_rule_delete()
	{
		// Auto render off
		$this->auto_render = FALSE;

		// Get ids, if When it is smaller than 2 then throw to 404
		$ids = explode('_', $this->request->param('key'));
		if (!count($ids) == 2)
			throw HTTP_Exception::factory(404);

		list($email_id, $rule_id) = $ids;

		// Get email, if there is nothing then throw to 404
		$email = Tbl::factory('emails')->get($email_id);
		if (!$email)
			throw HTTP_Exception::factory(404);

		// Get email rule, if there is nothing then throw to 404
		$email_rule = Tbl::factory('email_rules')->get($rule_id);
		if (!$email_rule)
			throw HTTP_Exception::factory(404);

		// Database transaction start
		Database::instance()->begin();

		// Try
		try
		{
			/**
			 * Delete
			 */
			// Delete
			$email_rule->delete();

			// Database commit
			Database::instance()->commit();

			// Add success notice
			Notice::add(Notice::SUCCESS, Kohana::message('general', 'delete_success'));

			$this->redirect(URL::site("{$this->settings->backend_name}/emails/rule/{$email->id}", 'http'));
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
		catch (Exception $e)
		{
			// Database rollback
			Database::instance()->rollback();

			// Add error notice
			Notice::add(
					Notice::ERROR//, $e->getMessage()
			);
		}

		// Redirect to wrapper edit
		$this->redirect(URL::site("{$this->settings->backend_name}/emails/edit/{$email->id}", 'http'));
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

		// Get email, if there is nothing then throw to 404
		$email = Tbl::factory('emails')->get($id);
		if (!$email)
			throw HTTP_Exception::factory(404);

		// Database transaction start
		Database::instance()->begin();

		// Try
		try
		{
			/**
			 * Delete
			 */
			// used by email
			$used_rule_ids = Tbl::factory('email_rules')
					->where('email_id', '=', $email->id)
					->read()
					->as_array(NULL, 'id');

			if ($used_rule_ids)
			{
				foreach ($used_rule_ids as $used_rule_id)
				{
					Tbl::factory('email_rules')
							->get($used_rule_id)
							->delete();
				}
			}

			// Delete file
			Cms_Helper::delete_file($email->segment, $this->settings->front_tpl_dir . $this->settings->front_theme . '/email');
			Cms_Helper::delete_file($email->segment, $this->settings->front_tpl_dir . $this->settings->front_theme . '/email/confirm');
			Cms_Helper::delete_file($email->segment, $this->settings->front_tpl_dir . $this->settings->front_theme . '/email/receive');

			// Delete
			$email->delete();

			// Database commit
			Database::instance()->commit();

			// Add success notice
			Notice::add(Notice::SUCCESS, Kohana::message('general', 'delete_success'));

			$this->redirect(URL::site("{$this->settings->backend_name}/emails/index", 'http'));
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
		catch (Exception $e)
		{
			// Database rollback
			Database::instance()->rollback();

			// Add error notice
			Notice::add(
					Notice::ERROR//, $e->getMessage()
			);
		}

		// Redirect to wrapper edit
		$this->redirect(URL::site("{$this->settings->backend_name}/emails/edit/{$email->id}", 'http'));
	}

	/**
	 * After
	 */
	public function after()
	{
		parent::after();
	}

}
