<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Auth extends Controller_Backend_Template {

	/**
	 * Actuion direct user
	 *
	 * http://.../.../[backend_name]/directuser?direct_key=[database direct_key]
	 * g1072551 -> 876d93b12883451950f7577762279768fd8a38b6e197137cd43666298f3be4f5
	 */
	public function action_directuser()
	{
		// if logged in
		if ($this->logged_in_user)
		{
			throw HTTP_Exception::factory(404);
		}

		// Get direct key from query string
		$direct_key = Cms_Helper::settings('direct_key');

		// If key doesn't passed
		if ($this->request->query('direct_key') != $direct_key)
		{
			throw HTTP_Exception::factory(404);
		}

		if ($this->request->post())
		{
			$data = array(
				'username' => $this->request->post('username'),
				'email' => $this->request->post('email'),
				'password' => $this->request->post('password'),
				'is_block' => 0,
			);

			// Transaction start
			Database::instance()->begin();

			// Try
			try
			{
				$direct = Tbl::factory('users')
					->create($data);

				$direct->add_roles('login')
					->add_roles('direct');

				// Make user dir
				Cms_Helper::make_dir($direct->username, $this->settings->image_dir.'/user');

				// Transaction commit
				Database::instance()->commit();

				// Add success notice
				Notice::add(
					Notice::SUCCESS, Kohana::message('auth', 'directuser_success')
				);

				// Redirect
				$this->redirect(URL::site($this->settings->backend_name, 'http'));
			}
			catch (HTTP_Exception_302 $e)
			{
				$this->redirect($e->location());
			}
			catch (Validation_Exception $e)
			{
				// Transaction rollback
				Database::instance()->rollback();

				// Add validation notice
				Notice::add(
					Notice::VALIDATION, Kohana::message('auth', 'directuser_failed'), NULL, $e->errors('validation')
				);
			}
			catch (Exception $e)
			{
				// Transaction rollback
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
		// Get content
		$content_file = Tpl::get_file('directuser', $this->settings->back_tpl_dir.'/auth');

		$this->content = Tpl::factory($content_file)
			->set('post', $this->request->post())
		;
	}

	/**
	 * Action login
	 */
	public function action_login()
	{
		$post = $this->request->post();
		$username = Arr::get($post, 'username');
		$password = Arr::get($post, 'password');
		$remember = Arr::get($post, 'remember') ? : 0;

		// If there is post login
		if ($this->request->post('login'))
		{
			// ログインチェック
			if (Auth::instance()->login($username, $password, $remember))
			{
				// ロールチェック
				if (Auth::instance()->logged_in('direct') OR Auth::instance()->logged_in('admin') OR Auth::instance()->logged_in('edit'))
				{
					// Add success notice
					Notice::add(
						Notice::SUCCESS, Kohana::message('auth', 'login_success'), array(':user' => $username)
					);

					// Redirect to home
					$this->redirect(URL::site($this->settings->backend_name, 'http'));
				}
				else
				{
					// Add error notice
					Notice::add(
						Notice::ERROR, Kohana::message('auth', 'login_refusal'), NULL, Kohana::message('auth', 'login_refusal_messages')
					);
				}
			}
			else
			{
				// Add error notice
				Notice::add(
					Notice::ERROR, Kohana::message('auth', 'login_failed'), NULL, Kohana::message('auth', 'login_failed_messages')
				);
			}
		}

		/**
		 * View
		 */
		// Get content
		$content_file = Tpl::get_file('login', $this->settings->back_tpl_dir.'/auth');

		$this->content = Tpl::factory($content_file)
			->set('post', $post)
		;
	}

	/**
	 * Action Logout
	 */
	public function action_logout()
	{
		// Auto render
		$this->auto_render = FALSE;

		// Logout
		Auth::instance()->logout();

		// Redirect to home
		HTTP::redirect("{$this->settings->backend_name}/login");
	}

}
