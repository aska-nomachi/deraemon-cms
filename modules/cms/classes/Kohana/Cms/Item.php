<?php

defined('SYSPATH') OR die('No direct script access.');

class Kohana_Cms_Item {

	/**
	 * Build html
	 *
	 * @param  object  $item
	 * @return string
	 */
	public static function build_html($item)
	{
		/*
		 *  :TODO aaaaaaaaaaaaaaaaaaaaaaa
		 */

		// Get settings
//		$settings = Cms_Helper::settings();
//
//		$wrapper_file_path = Kohana::find_file($settings->front_tpl_dir . '/wrapper', $item->wrapper->segment, 'html');
//		$wrapper_content = file_get_contents($wrapper_file_path);
//
//		$division_file_path = Kohana::find_file($settings->front_tpl_dir . '/division', $item->division->segment, 'html');
//		$division_content = file_get_contents($division_file_path);
//
//		$wd = str_replace('{{>division_content}}', $division_content, $wrapper_content);
//
//		$shape_file_path = Kohana::find_file($settings->front_tpl_dir . '/shape', $item->shape_segment, 'html');
//		$shape_content = $shape_file_path ? file_get_contents($shape_file_path) : '{{>item_content}}';
//
//		$wds = str_replace('{{>shape_content}}', $shape_content, $wd);
//
//		$item_file_path = Kohana::find_file($settings->item_dir . '/' . $item->division->segment, $item->segment, 'html');
//		$item_content = file_get_contents($item_file_path);
//
//		$wdsi = str_replace('{{>item_content}}', $item_content, $wds);
//		
//		$replacements = array();
//		preg_match_all("/{{>(.[^{}]*)}}/", $wdsi, $replacements, PREG_SET_ORDER);
//
//		// dirからpart名を取得
//		
//		
//		// $replacementsにpartがあれば置換え
//		
//		
//		// Get others $replacementsにあれば置換え
//		$others = array(
//			'search_form', 'search_result',
//			'comment_form', 'comment_result',
//			'comment_form', 'comment_result',
//			'author_login', 'author_register', 'author_activate', 'author_forgot', 'author_reset', 'author_resign', 'author_account', 'author_password', 'author_detail'
//		);
//		
//		// mailも
//		
//
//		$wdsip = $wdsi;
//		foreach ($parts as $part)
//		{
//			list($search, $replace_segment) = $part;
//			$part_file_path = Kohana::find_file($settings->front_tpl_dir . '/part', $replace_segment, 'html');
//			$part_content = file_get_contents($part_file_path);
//			$wdsip = str_replace($search, $part_content, $wdsip);
//		}
//		
//					echo Debug::vars($wdsip);
//
//		
//		die;

		/*
		 *  :TODO aaaaaaaaaaaaaaaaaaaaaaa
		 */


		// Get settings
		$settings = Cms_Helper::settings();

		// shapeを使っている時
		if ($item->shape_segment)
		{
			// Todo::-1 content考える！ パーシャルは先に変更しておく！？
			$shape_content = Tpl::get_file($item->shape_segment, $settings->front_tpl_dir . '/shape');
			$item->content = Tpl::get_file($item->segment, $settings->item_dir . '/' . $item->division->segment);
		}
		else
		{
			$shape_content = Tpl::get_file($item->segment, $settings->item_dir . '/' . $item->division->segment);
			$item->content = '';
		}

		$division_content = Tpl::get_file($item->division->segment, $settings->front_tpl_dir . '/division', array('shape_content' => $shape_content));

		$wrapper_content = Tpl::get_file($item->wrapper->segment, $settings->front_tpl_dir . '/wrapper', array('division_content' => $division_content));

		// Search parts
		preg_match_all("/{{>(.[^{}]*)}}/", $wrapper_content, $matches, PREG_SET_ORDER);

		// Get others
		$others = array(
			'search_form', 'search_result',
			'comment_form', 'comment_result',
			'comment_form', 'comment_result',
			'author_login', 'author_register', 'author_activate', 'author_forgot', 'author_reset', 'author_resign', 'author_account', 'author_password', 'author_detail'
		);

		// Get email segments
		$emails = Tbl::factory('emails')
				->read()
				->as_array(NULL, 'segment');

		$parts = array();
		foreach ($matches as $matche)
		{
			$key = $matche[0];
			$segment_string = $matche[1];

			// If parts tag in others
			if (in_array($segment_string, $others))
			{
				list($dir, $file) = explode('_', $segment_string);
				$parts[$key] = Tpl::get_file($file, $settings->front_tpl_dir . '/' . $dir);
			}
			// If parts tag in emails array
			elseif (in_array(str_replace('email_', '', $segment_string), $emails))
			{
				$segment = str_replace('email_', '', $segment_string);
				// {{@segment}}をsegmentに変更
				$email_content = Tpl::get_file($segment, $settings->front_tpl_dir . '/email');
				$parts[$key] = str_replace('{{@segment}}', $segment, $email_content);
			}
			else
			{
				$parts[$key] = Tpl::get_file($matche[1], $settings->front_tpl_dir . '/part');
			}
		}

		// Replace parts
		$html = str_replace(array_keys($parts), array_values($parts), $wrapper_content);

		return $html;
	}

	/**
	 * text filter
	 *
	 * @param  string  $values
	 * @param  array	$allowable_tags
	 * @return string
	 */
	public static function post_filter($values, $allowable_tags = NULL)
	{
		// Todo:: ここ見直し！?
		foreach ($values as &$value)
		{
			$value = str_replace(array('{', '}'), array('', ''), $value);
			$value = strip_tags($value, $allowable_tags);
			//$value = HTML::chars($value);
		}

		return $values;
	}

	/**
	 * User Login
	 *
	 * @return object
	 * 					post
	 * 					success
	 * 					failed
	 * 					errors
	 */
	public static function login($post)
	{
		// post filter
		$post = self::post_filter($post);

		// Build result
		$result = new stdClass();
		$result->post = $post;
		$result->success = FALSE;
		$result->failed = FALSE;
		//$result->errors = array();

		$username = NULL;
		$password = Arr::get($post, 'password');
		$remember = Arr::get($post, 'remember');

		// If usernameがemailの場合
		if (Valid::email(Arr::get($post, 'username')))
		{
			$user = Tbl::factory('users')
					->where('email', '=', Arr::get($post, 'username'))
					->read(1);

			//If usernameとemailが同じでないとき
			if (($user->username) !== $user->email)
			{
				$username = $user->username;
			}
			//Else 同じじゃないとき
			else
			{
				$username = Arr::get($post, 'username');
			}
		}
		// Else usernameがemailでないとき
		else
		{
			$username = Arr::get($post, 'username');
		}

		// Check login!
		if (Auth::instance()->login($username, $password, $remember))
		{
			/**
			 * Set result
			 */
			$result->post = array();
			$result->success = TRUE;
		}
		else
		{
			// Result
			$result->failed = TRUE;
		}

		Session::instance()->set('login_result', $result);
	}

	/**
	 * User Logout
	 *
	 * @return object
	 * 					success
	 */
	public static function logout()
	{
		// Build result
		$result = new stdClass();
		$result->success = TRUE;

		// Logout
		Auth::instance()->logout();
		Session::instance()->set('logout_result', $result);
	}

	/**
	 * User register
	 *
	 * @return object
	 * 					post
	 * 					activate
	 * 					success
	 * 					invalid
	 * 					exception
	 * 					errors
	 */
	public static function register($post)
	{
		/*
		 * Check onetime ticket
		 */
		// <editor-fold defaultstate="collapsed" desc="Check onetime ticket">
		$session_ticket = Session::instance()->get_once('ticket');
		$post_ticket = Arr::get($post, 'ticket');

		if (!$session_ticket OR ! $post_ticket OR $session_ticket !== $post_ticket)
		{
			HTTP::redirect(Request::current()->referrer());
		}
		// </editor-fold>
		//
		/*
		 * Register
		 */
		// post filter
		$post = self::post_filter($post);

		// Build result
		$result = new stdClass();
		$result->post = $post;
		$result->activate = NULL;
		$result->success = FALSE;
		$result->invalid = FALSE;
		$result->exception = FALSE;
		$result->errors = array();

		// Get setting
		$settings = Cms_Helper::settings();

		// Get set variable
		$username = Arr::get($post, 'username');
		$email = Arr::get($post, 'email');
		$password = Arr::get($post, 'password');

		// Database transaction start
		Database::instance()->begin();

		/**
		 * Try
		 */
		try
		{
			// Todo:: IDが飛ぶから修正
			// Create
			Tbl::factory('users')
					->create(array(
						'username' => $username,
						'email' => $email,
						'password' => $password,
						'is_block' => $settings->author_register_default_is_block,
					))
					->add_roles('login');

			/**
			 * Set result
			 */
			$result->post = array();
			$result->success = TRUE;
		}
		catch (Validation_Exception $e)
		{
			// Result
			$result->invalid = TRUE;

			// Separate errors field and message
			$errors = $e->errors('validation');

			foreach ($errors as $key => $value)
			{
				$result->errors[] = array('field' => $key, 'message' => $value);
			}
		}
		catch (Exception $e)
		{
			// Result
			$result->exception = TRUE;

			// errors
			//$result->errors = array(
			//	'field' => 'system error',
			//	'message' => $e->getMessage(),
			//	'file' => $e->getFile(),
			//	'line' => $e->getLine(),
			//);
		}

		/**
		 * If success is true
		 */
		if ($result->success)
		{
			// If メールチェックあるとき
			if ($settings->author_register_activate_is_on)
			{
				// メールチェックがあるフラッグ
				$result->activate = TRUE;

				// Todo:: IDが飛ぶから修正
				// Database rollback 一度戻してメールを送る
				Database::instance()->rollback();

				// user activate keyを作成
				// delimiterを取得
				$delimiter = $settings->author_register_activate_key_delimiter;

				// author_register_activate_access_keyとusername、email、passwordをdelimiterでつないで、
				// -> 暗号化
				// -> URLエンコード
				// -> クエリストリングにする
				$activate_key = '?activate_key=' . urlencode(
								Encrypt::instance()
										->encode($settings->author_register_activate_access_key . $delimiter . $username . $delimiter . $email . $delimiter . $password)
				);

				// Get message and clean return
				$activate_content = preg_replace('/(\r\n|\n|\r)/', '', Tpl::get_file('activate_mail', $settings->front_tpl_dir . '/author'));

				$activate_message = Tpl::factory($activate_content)
						->set('username', $username)
						->set('email', $email)
						->set('password', $password)
						->set('activate_key', $activate_key)
						->render();

				// メールを送る
				// Set time limit to 5 minutes
				set_time_limit(360);

				// Todo:: これをコメントアウトして下のコメント解除
				//echo '<meta charset="utf-8"><pre>'.$activate_message.'</pre>';
				// Todo:: 送信チェック！
				Email::factory($settings->author_register_activate_subject, $activate_message, $settings->author_register_activate_email_type)
						->set_config(array(
							'driver' => 'smtp',
							'options' => array(
								'hostname' => $settings->smtp_hostname,
								'username' => $settings->smtp_username,
								'password' => $settings->smtp_password,
								'port' => $settings->smtp_port,
							)
						))
						->to($email)
						// Todo:: 追加
						->cc($settings->author_register_activate_from_address, $settings->author_register_activate_from_name ? : NULL)
						->from($settings->author_register_activate_from_address, $settings->author_register_activate_from_name ? : NULL)
						->send();
			}
			// Else メールチェックないとき
			else
			{
				// メールチェックがあるフラッグなし
				$result->activate = FALSE;

				// Database commit
				Database::instance()->commit();
			}
		}

		/**
		 * If failed is true
		 */
		if (!$result->success)
		{
			// Database rollback
			Database::instance()->rollback();
		}

		// result set to session
		Session::instance()->set('register_result', $result);
	}

	/**
	 * User activate
	 *
	 * @return object
	 * 					query
	 * 					success
	 * 					invalid
	 * 					exception
	 */
	public static function activate($get)
	{
		// Build result
		$result = new stdClass();
		$result->query = $get;
		$result->success = FALSE;
		$result->invalid = FALSE;
		$result->exception = FALSE;
		$result->errors = array();

		// Get user activate key
		$activate_key = Arr::get($get, 'activate_key');

		// Get settings
		$settings = Cms_Helper::settings();

		// Database transaction start
		Database::instance()->begin();

		/**
		 * Try
		 */
		try
		{
			// If user activate keyがないときはエラー
			if (!$activate_key)
			{
				throw new Kohana_Exception('user activate key is noting.');
			}

			// ->query()なのでURLデコードいらない！
			// user active key 暗号解除 -> delimiterで分割
			list($author_register_activate_access_key, $username, $email, $password) = explode($settings->author_register_activate_key_delimiter, Encrypt::instance()->decode($activate_key));

			// If アクセスキーが違うときはエラー
			if ($author_register_activate_access_key !== $settings->author_register_activate_access_key)
			{
				throw new Kohana_Exception('user activate access key is noting.');
			}

			// Create
			$user = Tbl::factory('users')
					->create(array(
						'username' => $username,
						'email' => $email,
						'password' => $password,
						'is_block' => $settings->author_register_default_is_block,
					))
					->add_roles('login');

			// Create users_details
			$detail_ids = Tbl::factory('details')
					->read()
					->as_array(NULL, 'id');

			foreach ($detail_ids as $detail_id)
			{
				Tbl::factory('users_details')
						->create(array(
							'user_id' => $user->id,
							'detail_id' => $detail_id,
							'value' => NULL,
				));
			}

			// Make user dir
			Cms_Helper::make_dir($user->username, $settings->image_dir . '/user');
			//Cms_Helper::rename_dir($user->username, 'new', $settings->image_dir.'/user');
			//Cms_Helper::delete_dir($user->username, $settings->image_dir.'/user', TRUE);
			// make dir はbackendも！

			/**
			 * Set result
			 */
			$result->post = array();
			$result->success = TRUE;

			// Database commit
			Database::instance()->commit();
		}
		catch (Validation_Exception $e)
		{
			// Database rollback
			Database::instance()->rollback();

			// Result
			$result->invalid = TRUE;
		}
		catch (Exception $e)
		{
			// Database rollback
			Database::instance()->rollback();

			// Result
			$result->exception = TRUE;

			// errors
			//$result->errors = array(
			//	'field' => 'system error',
			//	'message' => $e->getMessage(),
			//	'file' => $e->getFile(),
			//	'line' => $e->getLine(),
			//);
		}

		// result set to session
		Session::instance()->set('activate_result', $result);
	}

	/**
	 * Password forgot
	 *
	 * @return object
	 * 					post
	 * 					success
	 * 					invalid
	 * 					exception
	 * 					errors
	 */
	public static function forgot($post)
	{
		/*
		 * Check onetime ticket
		 */
		// <editor-fold defaultstate="collapsed" desc="Check onetime ticket">
		$session_ticket = Session::instance()->get_once('ticket');
		$post_ticket = Arr::get($post, 'ticket');

		if (!$session_ticket OR ! $post_ticket OR $session_ticket !== $post_ticket)
		{
			HTTP::redirect(Request::current()->referrer());
		}
		// </editor-fold>
		//
		// post filter
		$post = self::post_filter($post);

		// Build result
		$result = new stdClass();
		$result->post = $post;
		$result->success = FALSE;
		$result->invalid = FALSE;
		$result->exception = FALSE;
		$result->errors = array();

		// Get settings
		$settings = Cms_Helper::settings();

		// Database transaction start
		Database::instance()->begin();

		/**
		 * Try
		 */
		try
		{
			// Validation email
			$validation = Validation::factory($post)
					->rule('email', 'not_empty')
					->rule('email', 'email')
					->rule('email', 'Tbl_Users::has_email')
					->label('email', __('Email'));

			// If Validation
			if (!$validation->check())
			{
				throw new Validation_Exception($validation);
			}

			// Get user あとでupdeteするからget()で取得
			$user = Tbl::factory('users')
					->where('email', '=', Arr::get($post, 'email'))
					->get();

			// If there is not user to exception ユーザーが登録されてないときは例外へ
			if (!$user)
			{
				throw new Kohana_Exception('there is not user.');
			}

			// Build reset_key
			$reset_key = Text::random('alnum', 32);

			// Set reset key to databese ここでリセットキーをアップデート
			$user->update(array('reset_key' => $reset_key));

			// $password reset keyを作成
			// delimiterを取得
			$delimiter = $settings->author_password_reset_key_delimiter;

			// reset_key、emailをdelimiterでつないで、
			// -> 暗号化
			// -> URLエンコード
			// -> クエリストリングにする
			$reset_key = '?reset_key=' . urlencode(Encrypt::instance()->encode($reset_key . $delimiter . $user->email));

			// Get message and clean return
			$reset_content = preg_replace('/(\r\n|\n|\r)/', '', Tpl::get_file('reset_mail', $settings->front_tpl_dir . '/author'));

			$reset_message = Tpl::factory($reset_content)
					->set('user', $user)
					->set('reset_key', $reset_key)
					->render();

			// メールを送る
			// Set time limit to 5 minutes
			set_time_limit(360);

			// Todo:: これをコメントアウトして下のコメント解除
			//echo '<meta charset="utf-8"><pre>'.$reset_message.'</pre>';
			// Todo:: 送信チェック！
			Email::factory($settings->author_password_reset_subject, $reset_message, $settings->author_password_reset_email_type)
					->set_config(array(
						'driver' => 'smtp',
						'options' => array(
							'hostname' => $settings->smtp_hostname,
							'username' => $settings->smtp_username,
							'password' => $settings->smtp_password,
							'port' => $settings->smtp_port,
						)
					))
					->to($user->email)
					->from($settings->author_password_reset_from_address, $settings->author_password_reset_from_name ? : NULL)
					->send();

			// Database commit
			Database::instance()->commit();

			/**
			 * Set result
			 */
			$result->post = array();
			$result->success = TRUE;
		}
		catch (Validation_Exception $e)
		{
			// Database rollback
			Database::instance()->rollback();

			// Result
			$result->invalid = TRUE;

			// Separate errors field and message
			$errors = $e->errors('validation');

			foreach ($errors as $key => $value)
			{
				$result->errors[] = array('field' => $key, 'message' => $value);
			}
		}
		catch (Exception $e)
		{
			// Database rollback
			Database::instance()->rollback();

			// Result
			$result->exception = TRUE;

			//errors
			//$result->errors = array(
			//	'field' => 'system error',
			//	'message' => $e->getMessage(),
			//	'file' => $e->getFile(),
			//	'line' => $e->getLine(),
			//);
		}

		Session::instance()->set('forgot_result', $result);
	}

	/**
	 * Password reset
	 *
	 * @return object
	 * 					post
	 * 					query
	 * 					success
	 * 					invalid
	 * 					exception
	 * 					errors
	 */
	public static function reset($post, $get)
	{
		// post filter
		$post = self::post_filter($post);

		// Build result
		$result = new stdClass();
		$result->post = $post;
		$result->get = $get;
		$result->success = FALSE;
		$result->invalid = FALSE;
		$result->exception = FALSE;
		$result->errors = array();

		// Get settings
		$settings = Cms_Helper::settings();

		/*
		 * check reset key
		 */
		// <editor-fold defaultstate="collapsed" desc="check reset key">
		try
		{
			// Get password reset key
			$reset_key_string = Arr::get($get, 'reset_key');

			// If password reset keyがないときはエラー
			if (!$reset_key_string)
			{
				throw new Kohana_Exception('password reset key is noting.');
			}

			// ->query()なのでURLデコードいらない！
			// active key  -> 暗号解除 -> delimiterで分割
			list($reset_key, $email) = explode($settings->author_password_reset_key_delimiter, Encrypt::instance()->decode($reset_key_string));

			// userをemailから取得
			$user = Tbl::factory('users')
					->where('email', '=', $email)
					->get();

			// userが取得できないとき
			if (!$user)
			{
				throw new Kohana_Exception('there is not user.');
			}

			// If リセット キーが違うときはエラー
			if ($reset_key !== $user->reset_key)
			{
				throw new Kohana_Exception('user activate access key is noting.');
			}
		}
		catch (Exception $e)
		{
			// Result
			$result->exception = TRUE;

			// errors
			//$result->errors = array(
			//	'field' => 'system error',
			//	'message' => $e->getMessage(),
			//	'file' => $e->getFile(),
			//	'line' => $e->getLine(),
			//);
		}
		// </editor-fold>

		/*
		 * If post
		 */
		// <editor-fold defaultstate="collapsed" desc="If post">
		// postがあって$result->exceptionがTRUEじゃないとき
		if (Arr::get($post, 'reset') AND ! $result->exception)
		{
			/*
			 * Check onetime ticket
			 */
			// <editor-fold defaultstate="collapsed" desc="Check onetime ticket">
			$session_ticket = Session::instance()->get_once('ticket');
			$post_ticket = Arr::get($post, 'ticket');

			if (!$session_ticket OR ! $post_ticket OR $session_ticket !== $post_ticket)
			{
				HTTP::redirect(Request::current()->referrer());
			}
			// </editor-fold>
			//
			// Database transaction start
			Database::instance()->begin();

			/*
			 * Try for post
			 */
			try
			{
				/**
				 * password setting
				 */
				$validation = Validation::factory($post)
						->rule('password', 'not_empty')
						->rule('password', 'min_length', array(':value', 8))
						->rule('confirm', 'matches', array(':validation', 'confirm', 'password'))
						->label('password', __('Password'))
						->label('confirm', __('Confirm'));

				// If validation check is false
				if (!$validation->check())
					throw new Validation_Exception($validation);

				$user->update(array(
					'password' => Arr::get($post, 'password'),
					'reset_key' => NULL,
				));

				// Database commit
				Database::instance()->commit();

				/**
				 * Set result
				 */
				$result->post = array();
				$result->success = TRUE;
			}
			catch (Validation_Exception $e)
			{
				// Database rollback
				Database::instance()->rollback();

				// Result
				$result->invalid = TRUE;

				// Separate errors field and message
				$errors = $e->errors('validation');

				foreach ($errors as $key => $value)
				{
					$result->errors[] = array('field' => $key, 'message' => $value);
				}
			}
			catch (Exception $e)
			{
				// Database rollback
				Database::instance()->rollback();

				// Result
				$result->exception = TRUE;

				// errors
				//$result->errors = array(
				//	'field' => 'system error',
				//	'message' => $e->getMessage(),
				//	'file' => $e->getFile(),
				//	'line' => $e->getLine(),
				//);
			}
		}
		// </editor-fold>

		Session::instance()->set('reset_result', $result);
	}

	/**
	 * Resign
	 *
	 * @return object
	 * 					post
	 * 					success
	 * 					invalid
	 * 					exception
	 * 					errors
	 *
	 * 					デリートしないで、is_block = 1 にする
	 */
	public static function resign($post)
	{
		/*
		 * Check onetime ticket
		 */
		// <editor-fold defaultstate="collapsed" desc="Check onetime ticket">
		$session_ticket = Session::instance()->get_once('ticket');
		$post_ticket = Arr::get($post, 'ticket');

		if (!$session_ticket OR ! $post_ticket OR $session_ticket !== $post_ticket)
		{
			HTTP::redirect(Request::current()->referrer());
		}
		// </editor-fold>
		//
		// post filter
		$post = self::post_filter($post);

		// Build result
		$result = new stdClass();
		$result->post = $post;
		$result->success = FALSE;
		$result->invalid = FALSE;
		$result->exception = FALSE;

		// Database transaction start
		Database::instance()->begin();

		/**
		 * Try
		 */
		try
		{
			// user not login
			if (!Auth::instance()->logged_in())
			{
				throw new Kohana_Exception('user not loggin.');
			}

			// validation
			$validation = Validation::factory($post)
					->rule('username', 'not_empty')
					->rule('password', 'not_empty')
					->label('username', __('Username'))
					->label('password', __('Password'));

			// If validation check is false
			if (!$validation->check())
				throw new Validation_Exception($validation);

			// Get user
			$user = Tbl::factory('users')
					->where('id', '=', Auth::instance()->get_user()->id)
					->get();

			// password hash
			$resign_password = Auth::instance()->hash_password(Arr::get($post, 'password'));

			// check password and username
			if ($user->password !== $resign_password OR $user->username !== Arr::get($post, 'username'))
			{
				throw new Kohana_Exception('password or usernane were not match.');
			}

			$user->update(array(
				'is_block' => 1,
			));

			// logout
			Auth::instance()->logout();

			/**
			 * Set result
			 */
			$result->post = array();
			$result->success = TRUE;

			// Database commit
			Database::instance()->commit();
			Session::instance()->set('reset_result', $result);
			HTTP::redirect(URL::site('author/resign', 'http'));
		}
		catch (Validation_Exception $e)
		{
			// Database rollback
			Database::instance()->rollback();

			// Resutl
			$result->invalid = TRUE;

			// Separate errors field and message
			$errors = $e->errors('validation');

			foreach ($errors as $key => $value)
			{
				$result->errors[] = array('field' => $key, 'message' => $value);
			}
		}
		catch (Exception $e)
		{
			// Result
			$result->exception = TRUE;

			// errors
			//$result->errors = array(
			//	'field' => 'system error',
			//	'message' => $e->getMessage(),
			//	'file' => $e->getFile(),
			//	'line' => $e->getLine(),
			//);
		}

		Session::instance()->set('resign_result', $result);
	}

	/**
	 * Password
	 *
	 * @return object
	 * 					post
	 * 					success
	 * 					failed
	 * 					errors
	 */
	public static function account($post)
	{
		/*
		 * Check onetime ticket
		 */
		// <editor-fold defaultstate="collapsed" desc="Check onetime ticket">
		$session_ticket = Session::instance()->get_once('ticket');
		$post_ticket = Arr::get($post, 'ticket');

		if (!$session_ticket OR ! $post_ticket OR $session_ticket !== $post_ticket)
		{
			//HTTP::redirect(Request::current()->referrer());
		}
		// </editor-fold>
		// Build result
		$result = new stdClass();
		$result->post = $post;
		$result->success = FALSE;
		$result->invalid = FALSE;
		$result->exception = FALSE;
		$result->errors = array();

		// Try
		try
		{

			// Save old file
			$oldname = Auth::instance()->get_user()->username;

			// Get settings
			$settings = Cms_Helper::settings();

			// $_FILESがなくて$postがabatar_deleteを持ってない時
			if (!isset($_FILES['avatar']) AND ! Arr::get($post, 'avatar_delete'))
			{
				// Update
				$user = Tbl::factory('users')
						->get(Auth::instance()->get_user()->id)
						->update($post);

				// New name
				$newname = $user->username;

				// Rename image user dir
				Cms_Helper::rename_dir($oldname, $newname, $settings->image_dir . '/user');
			}
			// $_FILESがあって$postがabatar_deleteを持ってない時
			elseif (Upload::not_empty($_FILES['avatar']) AND ! Arr::get($post, 'avatar_delete'))
			{
				// Set post
				$post['avatar'] = $_FILES['avatar'];

				// Get image type
				$post['ext'] = NULL;
				switch ($post['avatar']['type'])
				{
					case 'image/jpeg':
						$post['ext'] = '.jpg';
						break;

					case 'image/png':
						$post['ext'] = '.png';
						break;

					case 'image/gif':
						$post['ext'] = '.gif';
						break;

					default:
						$post['ext'] = NULL;
						break;
				}

				// Update
				$user = Tbl::factory('users')
						->get(Auth::instance()->get_user()->id)
						->update($post, 'validate_with_avatar');

				// New name
				$newname = $user->username;

				// Rename image user dir
				Cms_Helper::rename_dir($oldname, $newname, $settings->image_dir . '/user');

				// Image division directory // イメージを入れるディレクトリ
				$dir_path = 'application/' . $settings->image_dir . '/user/' . $user->username . '/';
				// Upload image イメージをアップロード
				$filename = Upload::save($post['avatar'], 'avatar' . $user->ext, $dir_path);

				// Build sizes
				$sizes = array(
					'_v' => explode(',', str_replace(' ', '', $settings->image_v)),
					'_h' => explode(',', str_replace(' ', '', $settings->image_h)),
					'_s' => explode(',', str_replace(' ', '', $settings->image_s)),
				);

				// Resize image 他のサイズを作成
				foreach ($sizes as $key => $value)
				{
					Image::factory($filename)
							->resize($value[0], $value[1], Image::INVERSE)
							->crop($value[0], $value[1])
							->save($dir_path . 'avatar' . $key . $user->ext);
				}
			}
			else
			{
				// Get user
				$user = Auth::instance()->get_user();

				// Get directory
				$dir_path = 'application/' . $settings->image_dir . '/user/' . $user->username . '/';

				// Delete image files
				if (is_file($dir_path . 'avatar' . $user->ext))
				{
					unlink($dir_path . 'avatar' . $user->ext);
					unlink($dir_path . 'avatar' . '_v' . $user->ext);
					unlink($dir_path . 'avatar' . '_h' . $user->ext);
					unlink($dir_path . 'avatar' . '_s' . $user->ext);
				}

				// Set NULL to post ext
				$post['ext'] = NULL;

				// Update
				Tbl::factory('users')
						->get(Auth::instance()->get_user()->id)
						->update($post);
			}

			// Database commit
			Database::instance()->commit();

			Auth::instance()->logout();

			/**
			 * Set result
			 */
			$result->post = array();

			$result->success = TRUE;

			/**
			 * redirect
			 */
			Session::instance()->set('account_result', $result);

			// Todo:: oldとnewを比較、avatarだけならloginにいかないようにする？
			//HTTP::redirect(Request::current()->url('http'));
			HTTP::redirect(URL::site('author/login', 'http'));
		}
		catch (Validation_Exception $e)
		{
			// Database rollback
			Database::instance()->rollback();

			// Result
			$result->invalid = TRUE;

			// Separate errors field and message
			$errors = $e->errors('validation');

			foreach ($errors as $key => $value)
			{
				$result->errors[] = array('field' => $key, 'message' => $value);
			}
		}
		catch (HTTP_Exception_302 $e)
		{
			HTTP::redirect($e->location());
		}
		catch (Exception $e)
		{
			// Database rollback
			Database::instance()->rollback();

			// Result
			$result->exception = TRUE;

			// errors
			$result->errors[] = array(
				'field' => 'system error',
				'message' => $e->getMessage(),
				'file' => $e->getFile(),
				'line' => $e->getLine(),
			);
			//echo Debug::vars($result->errors);
		}

		Session::instance()->set('account_result', $result);
	}

	/**
	 * Password
	 *
	 * @return object
	 * 					post
	 * 					success
	 * 					failed
	 * 					errors
	 */
	public static function password($post)
	{
		/*
		 * Check onetime ticket
		 */
		// <editor-fold defaultstate="collapsed" desc="Check onetime ticket">
		$session_ticket = Session::instance()->get_once('ticket');
		$post_ticket = Arr::get($post, 'ticket');

		if (!$session_ticket OR ! $post_ticket OR $session_ticket !== $post_ticket)
		{
			HTTP::redirect(Request::current()->referrer());
		}
		// </editor-fold>
		// Build result
		$result = new stdClass();
		$result->post = $post;
		$result->success = FALSE;
		$result->invalid = FALSE;
		$result->exception = FALSE;
		$result->errors = array();

		// Try
		try
		{
			$validation = Validation::factory($post)
					->rule('newer', 'not_empty')
					->rule('newer', 'min_length', array(':value', 8))
					->rule('confirm', 'matches', array(':validation', ':field', 'newer'))
					->rule('present', 'not_empty')
					->rule('present', 'Tbl_Users::check_pass')
					->label('present', __('Present Password'))
					->label('newer', __('Newer Password'))
					->label('confirm', __('Confirm Password'))
			;

			if (!$validation->check())
			{
				throw new Validation_Exception($validation);
			}

			/**
			 * Update user
			 */
			$data = array(
				'password' => $post['newer'],
			);
			Tbl::factory('users')
					->get(Auth::instance()->get_user()->id)
					->update($data);

			// Database commit
			Database::instance()->commit();

			/**
			 * Set result
			 */
			$result->post = '';

			$result->success = TRUE;

			/**
			 * logout and redirect
			 */
			Auth::instance()->logout();
			Session::instance()->set('password_result', $result);
			HTTP::redirect(URL::site('author/login', 'http'));
		}
		catch (Validation_Exception $e)
		{
			// Database rollback
			Database::instance()->rollback();

			// Result
			$result->invalid = TRUE;

			// Separate errors field and message
			$errors = $e->errors('validation');

			foreach ($errors as $key => $value)
			{
				$result->errors[] = array('field' => $key, 'message' => $value);
			}
		}
		catch (HTTP_Exception_302 $e)
		{
			HTTP::redirect($e->location());
		}
		catch (Exception $e)
		{
			// Database rollback
			Database::instance()->rollback();

			// Result
			$result->exception = TRUE;

			// errors
//			$result->errors[] = array(
//				'field' => 'system error',
//				'message' => $e->getMessage(),
//				'file' => $e->getFile(),
//				'line' => $e->getLine(),
//			);
		}

		Session::instance()->set('password_result', $result);
	}

	/**
	 * Detail
	 *
	 * @return object
	 * 					post
	 * 					success
	 * 					failed
	 * 					errors
	 */
	public static function detail($post)
	{
		/*
		 * Check onetime ticket
		 */
		// <editor-fold defaultstate="collapsed" desc="Check onetime ticket">
//		$session_ticket = Session::instance()->get_once('ticket');
//		$post_ticket = Arr::get($post, 'ticket');
//
//		if (!$session_ticket OR ! $post_ticket OR $session_ticket !== $post_ticket)
//		{
//			HTTP::redirect(Request::current()->referrer());
//		}
		// </editor-fold>

		/*
		 * update
		 */

		// Get user
		$user = Auth::instance()->get_user();

		// Get user detail
		$detail_items = Tbl::factory('users_details')
				->join('details')->on('users_details.detail_id', '=', 'details.id')
				->select('users_details.*')
				->select('details.segment')
				->select('details.name')
				->where('user_id', '=', $user->id)
				->read()
				->as_array('segment');

		// Set post to detail_items
		$post_for_validation = array();
		foreach ($detail_items as $detail_item)
		{
			if (isset($post[$detail_item->segment]))
			{
				// Set post to detail_item and split tag and trim
				$detail_item->value = trim(strip_tags($post[$detail_item->segment]));

				// Set post to post_for_validation
				$post_for_validation[$detail_item->segment] = $detail_item->value;
			}
		}

		// Build result
		$result = new stdClass();
		$result->post = NULL;
		$result->success = FALSE;
		$result->invalid = FALSE;
		$result->exception = FALSE;
		$result->errors = array();

		// Set result post
		$result->post = $detail_items;

		// Database transaction start
		Database::instance()->begin();

		// Try
		try
		{
			/**
			 * Validation
			 */
			// <editor-fold defaultstate="collapsed" desc="Validation">
			// Validation factory
			$validation = Validation::factory($post_for_validation);

			// Get rules ここで使うルール以外は取得しない！
			$detail_segments = $post_for_validation ? array_keys($post_for_validation) : NULL;
			$rules = Tbl::factory('detail_rules')
					->join('details')->on('detail_rules.detail_id', '=', 'details.id')
					->select('detail_rules.*')
					->select('details.segment')
					->select('details.name')
					->select('details.order')
					->where('details.segment', 'IN', $detail_segments)
					->read()
					->as_array();

			// Iterate post set rule and label
			foreach ($rules as $rule)
			{
				// if param is null or 0 or ''
				if (!$rule->param)
				{
					$rule->param = NULL;
				}
				else
				{
					$rule->param = explode(',', $rule->param);
					foreach ($rule->param as &$param)
					{
						$param = trim($param);
					}
				}
				$validation
						->rule($rule->segment, $rule->callback, $rule->param)
						->label($rule->segment, __($rule->name));
			}

			// Check validation
			if (!$validation->check())
			{
				throw new Validation_Exception($validation);
			}
			// </editor-fold>

			/**
			 * Update user
			 */
			foreach ($detail_items as $detail_item)
			{
				Tbl::factory('users_details')
						->select('users_details.*')
						->join('details')->on('users_details.detail_id', '=', 'details.id')
						->where('users_details.user_id', '=', $user->id)
						->where('details.segment', '=', $detail_item->segment)
						->get()
						->update(array('value' => $detail_item->value));
			}

			// Database commit
			Database::instance()->commit();

			$result->success = TRUE;
		}
		catch (Validation_Exception $e)
		{
			// Database rollback
			Database::instance()->rollback();

			// Result
			$result->invalid = TRUE;

			// Separate errors field and message
			$errors = $e->errors('validation');

			foreach ($errors as $key => $value)
			{
				$result->errors[] = array('field' => $key, 'message' => $value);
			}
		}
		catch (Exception $e)
		{
			// Database rollback
			Database::instance()->rollback();

			// Result
			$result->exception = TRUE;

			// errors
			$result->errors[] = array(
				'field' => 'system error',
				'message' => $e->getMessage(),
				'file' => $e->getFile(),
				'line' => $e->getLine(),
			);
		}

		Session::instance()->set('detail_result', $result);
	}

	/**
	 * Send email
	 *
	 * @return object
	 * 					post
	 * 					success
	 * 					invalid
	 * 					exception
	 * 					errors
	 *
	 * <input type="hidden" name="send_email_segment" value="[emailsのsegment]">
	 * <button type="submit" name="send_email[固定]" value="1">send</button>
	 * OR
	 * <button type="submit" name="send_email[固定]" value="[emailsのsegment]">send</button>
	 */
	public static function send_email($post)
	{
		/*
		 * Check onetime ticket
		 */
		// <editor-fold defaultstate="collapsed" desc="Check onetime ticket">
//		$session_ticket = Session::instance()->get_once('ticket');
//		$post_ticket = Arr::get($post, 'ticket');
//
//		if (!$session_ticket OR ! $post_ticket OR $session_ticket !== $post_ticket)
//		{
//			HTTP::redirect(Request::current()->referrer());
//		}
		// </editor-fold>
		//
		// Get settings
		$settings = Cms_Helper::settings();

		// post filter メールに含めるタグをフィルター
		$post = self::post_filter($post, $settings->send_email_allowable_tags);

		// Build result
		$result = new stdClass();
		$result->post = $post;
		$result->success = FALSE;
		$result->invalid = FALSE;
		$result->exception = array();
		$result->errors = array();

		// Get email
		$segment = Arr::get($post, 'send_email_segment', Arr::get($post, 'send_email'));

		$email = Tbl::factory('emails')
				->where('segment', '=', $segment)
				->read(1);

		// Try
		try
		{
			// If there is not email
			if (!$email)
			{
				throw new Kohana_Exception('there is not :send_email in mails.', array(':send_email' => $post['send_email']));
			}

			// Get rules
			$rules = Tbl::factory('email_rules')
					->where('email_id', '=', $email->id)
					->read()
					->as_array();

			/*
			 * validation
			 */
			// <editor-fold defaultstate="collapsed" desc="validation">
			$validation = Validation::factory($post);

			// Iterate post set rule and label
			foreach ($rules as $rule)
			{
				// if param is null or 0 or ''
				if (!$rule->param)
				{
					$rule->param = NULL;
				}
				else
				{
					$rule->param = explode(',', $rule->param);

					foreach ($rule->param as &$param)
					{
						$param = trim($param);
					}
				}
				$validation
						->rule($rule->field, $rule->callback, $rule->param)
						->label($rule->field, __($rule->label));
			}

			// If validation check is false
			if (!$validation->check())
				throw new Validation_Exception($validation);

			// </editor-fold>

			/*
			 * Send Receive
			 */
			// <editor-fold defaultstate="collapsed" desc="Receive">
			// Get receive subject ここでpostの値をpost名で使えるようにする。
			$receive_subject_factory = Tpl::factory($email->receive_subject);
			foreach ($post as $key => $value)
			{
				$receive_subject_factory->set($key, $value);
			}
			$receive_subject = $receive_subject_factory->render();

			// Get confirm message and clean return 改行は{{return}}でコントロールするためリターンを削除
			$receive_message_string = preg_replace('/(\r\n|\n|\r)/', '', Tpl::get_file($email->segment, $settings->front_tpl_dir . '/email/receive'));

			// Get receive content ここでpostの値をpost名で使えるようにする。
			$receive_content_factory = Tpl::factory($receive_message_string);
			foreach ($post as $key => $value)
			{
				$receive_content_factory->set($key, $value);
			}
			$receive_message = $receive_content_factory->render();

			$user_name = Arr::get($post, $email->user_name_field) ? : $settings->send_email_defult_user_name;
			$user_address = Arr::get($post, $email->user_address_field) ? : $settings->send_email_defult_user_address;

			// Set time limit to 5 minutes
			set_time_limit(360);

			// Todo:: これをコメントアウトして下のコメント解除
			//echo '<meta charset="utf-8">'."<pre>from: [$user_name] $user_address<br />subject: $receive_subject<br />$receive_message</pre>";
			// Todo:: 送信チェック！
			$receive_email = Email::factory($receive_subject, $receive_message, $email->receive_email_type)
					->set_config(array(
						'driver' => 'smtp',
						'options' => array(
							'hostname' => $settings->smtp_hostname,
							'username' => $settings->smtp_username,
							'password' => $settings->smtp_password,
							'port' => $settings->smtp_port,
						)
					))
					->to($email->admin_address, $email->admin_name)
					->from($user_address, $user_name);

			$receive_email->send();
			// </editor-fold>

			/*
			 * Send Confirm
			 */
			// <editor-fold defaultstate="collapsed" desc="Confirm">
			if ($settings->send_email_confirm_is_on AND Arr::get($post, $email->user_address_field))
			{
				// Get confirm subject ここでpostの値をpost名で使えるようにする。
				$confirm_subject_factory = Tpl::factory($email->confirm_subject);
				foreach ($post as $key => $value)
				{
					$confirm_subject_factory->set($key, $value);
				}
				$confirm_subject = $confirm_subject_factory->render();

				// Get confirm message and clean return
				$confirm_message_string = preg_replace('/(\r\n|\n|\r)/', '', Tpl::get_file($email->segment, $settings->front_tpl_dir . '/email/confirm'));

				// Get confirm content ここでpostの値をpost名で使えるようにする。
				$confirm_content_factory = Tpl::factory($confirm_message_string);
				foreach ($post as $key => $value)
				{
					$confirm_content_factory->set($key, $value);
				}
				$confirm_message = $confirm_content_factory->render();

				// Set time limit to 5 minutes
				set_time_limit(360);

				// Todo:: これをコメントアウトして下のコメント解除
				//echo '<meta charset="utf-8">'."<pre>from: [$email->admin_name] $email->admin_address<br />subject: $confirm_subject<br />$confirm_message</pre>";
				// Todo:: 送信チェック！
				Email::factory($confirm_subject, $confirm_message, $email->confirm_email_type)
						->set_config(array(
							'driver' => 'smtp',
							'options' => array(
								'hostname' => $settings->smtp_hostname,
								'username' => $settings->smtp_username,
								'password' => $settings->smtp_password,
								'port' => $settings->smtp_port,
							)
						))
						->to($user_address, $user_name)
						->from($email->admin_address, $email->admin_name)
						->send();
			}
			// </editor-fold>

			/*
			 * create received email
			 */
			// <editor-fold defaultstate="collapsed" desc="create received email">
			// データベースにメール内容を入れる
			if ($settings->send_email_save_is_on)
			{
				Tbl::factory('received_emails')
						->create(array(
							'email_segment' => $email->name,
							'json' => json_encode($post),
							'created' => Date::formatted_time(),
				));
			}
			// </editor-fold>

			/**
			 * Set result
			 */
			$result->post = array();
			$result->success = TRUE;
		}
		catch (Validation_Exception $e)
		{
			// Result
			$result->invalid = TRUE;

			// Separate errors field and message
			$errors = $e->errors('validation');

			foreach ($errors as $key => $value)
			{
				$result->errors[] = array('field' => $key, 'message' => $value);
			}
		}
		catch (Exception $e)
		{
			// Result
			$result->exception = TRUE;

			// errors
			$result->errors = array(
				'field' => 'system error',
				'message' => $e->getMessage(),
				'file' => $e->getFile(),
				'line' => $e->getLine(),
			);
			//echo Debug::vars($result->errors);
		}

		Session::instance()->set('send_email_result', $result);
	}

	/**
	 * Send comment
	 *
	 * @return object
	 * 					post
	 * 					success
	 * 					failed
	 * 					errors
	 */
	public static function send_comment($item_id, $post)
	{
		/*
		 * Check onetime ticket
		 */
		// <editor-fold defaultstate="collapsed" desc="Check onetime ticket">
		$session_ticket = Session::instance()->get_once('ticket');
		$post_ticket = Arr::get($post, 'ticket');

		if (!$session_ticket OR ! $post_ticket OR $session_ticket !== $post_ticket)
		{
			HTTP::redirect(Request::current()->referrer());
		}
		// </editor-fold>
		//
		//Get settings
		$settings = Cms_Helper::settings();

		$logged_in_user = Tbl::factory('users')
				->where('id', '=', Auth::instance()->get_user()->id)
				->read(1);

		// post filter
		$post = self::post_filter($post, $settings->send_comment_allowable_tags);

		// Build result
		$result = new stdClass();
		$result->post = $post;
		$result->success = FALSE;
		$result->invalid = FALSE;
		$result->exception = FALSE;
		$result->errors = array();

		// Database transaction start
		Database::instance()->begin();

		// Try
		try
		{
			// Create
			Tbl::factory('received_comments')
					->create(array(
						'item_id' => $item_id,
						'user_id' => isset($logged_in_user->id) ? $logged_in_user->id : NULL,
						'replay_id' => Arr::get($post, 'replay_id'),
						'display_name' => Arr::get($post, 'display_name'),
						'subject' => Arr::get($post, 'subject'),
						'content' => Arr::get($post, 'content'),
						'created' => Date::formatted_time(),
						'is_accept' => $settings->send_comment_is_accept_default,
			));

			// Database commit
			Database::instance()->commit();

			/**
			 * Set result
			 */
			$result->post = array();
			$result->success = TRUE;
		}
		catch (Validation_Exception $e)
		{
			// Database rollback
			Database::instance()->rollback();

			// Result
			$result->invalid = TRUE;

			// Separate errors field and message
			$errors = $e->errors('validation');

			foreach ($errors as $key => $value)
			{
				$result->errors[] = array('field' => $key, 'message' => $value);
			}
		}
		catch (Exception $e)
		{
			// Database rollback
			Database::instance()->rollback();

			// Result
			$result->exception = TRUE;

			// errors
			$result->errors[] = array(
				'field' => 'system error',
					//'message' => $e->getMessage(),
					//'file' => $e->getFile(),
					//'line' => $e->getLine(),
			);
		}

		Session::instance()->set('send_comment_result', $result);
	}

	/**
	 * search
	 *
	 * @return object
	 * 					query
	 * 					total
	 * 					pagenate
	 * 					items
	 *
	 * <form action="{{host}}test_search" method="GET">
	 * <input type="text" name="string" value="{{search_result.get.string}}" placeholder="string">
	 * <input type="hidden" name="and_or" value="and">デフォルトはand
	 *
	 * <input type="hidden" name="divisions" value="page, article">
	 * 又は
	 * <input type="hidden" name="divisions[]" value="page">
	 * <input type="hidden" name="divisions[]" value="shop">
	 *
	 * <input type="hidden" name="categories" value="aaa, bbb">
	 * 又は
	 * <input type="hidden" name="categories[]" value="aaa">
	 * <input type="hidden" name="categories[]" value="bbb">
	 *
	 * <input type="hidden" name="tag" value="aaa, bbb">
	 * 又は
	 * <input type="hidden" name="tag[]" value="aaa">
	 * <input type="hidden" name="tag[]" value="bbb">
	 *
	 * <input type="hidden" name="xn" value="3">
	 *
	 * <input type="hidden" name="paginate" value="4, 2"> [items_per_page, follow]
	 * <input type="hidden" name="order" value="name, DESC"> [order_column, order_direction]
	 *
	 * <input type="hidden" name="flags" value="images, fields, comments, children"> itemの何をとるか、デフォルトはfalse
	 *
	 * <button type="submit" name="search" value="search">search</button> nameはsearchで固定
	 * </form>
	 */
	public static function search($get)
	{
		// get filter
		$get = self::post_filter($get);

		// Build result
		$result = new stdClass();
		// kohanaのqueryだけどgetに入れる
		$result->get = $get;
		$result->total = NULL;
		$result->pagenate = NULL;
		$result->items = NULL;

		// パラメータを準備
		$string = Arr::get($get, 'string', '');
		$and_or = Arr::get($get, 'and_or', 'and');
		$divisions = Arr::get($get, 'divisions');
		$categories = Arr::get($get, 'categories');
		$tags = Arr::get($get, 'tags');

		$xn = Arr::get($get, 'xn');

		$paginate = Arr::get($get, 'paginate');
		$order = Arr::get($get, 'order');
		$flags = Arr::get($get, 'flags');

		// string タブスペースなんかを半角に置き換えてexplodeで分ける
		$strings = array_filter(explode(' ', preg_replace(array('/\s+/', '/,/', '/、/'), array(' ', ' ', ' '), mb_convert_kana($string, "s"))));

		// divisions
		if ($divisions)
		{
			$divisions = (!is_array($divisions)) ? explode(',', str_replace(' ', '', $divisions)) : $divisions;
		}

		// categories
		if ($categories)
		{
			$categories = (!is_array($categories)) ? explode(',', str_replace(' ', '', $categories)) : $categories;
		}

		// tags
		if ($tags)
		{
			$tags = (!is_array($tags)) ? explode(',', str_replace(' ', '', $tags)) : $tags;
		}

		// flags
		if ($flags)
		{
			$flags = (!is_array($flags)) ? explode(',', str_replace(' ', '', $flags)) : $flags;
		}
		else
		{
			$flags = array();
		}
		$images_flag = in_array('images', $flags);
		$fields_flag = in_array('fields', $flags);
		$comments_flag = in_array('comments', $flags);

		// sqlを作って実行
		// ストリングが有るとき時
		if ($strings)
		{
			// selectはitems.id, items.segmentのみ
			$sql = DB::select('items.id', 'items.segment')
							->from('items')
							->join('divisions')->on('items.division_id', '=', 'divisions.id')
							->join('items_categories', 'LEFT')->on('items.id', '=', 'items_categories.item_id')
							->join('categories', 'LEFT')->on('items_categories.category_id', '=', 'categories.id')
							->join('items_tags', 'LEFT')->on('items.id', '=', 'items_tags.item_id')
							->join('tags', 'LEFT')->on('items_tags.tag_id', '=', 'tags.id');

			// エディターから上位の時はすべて表示
			if (!(Auth::instance()->logged_in('direct') OR Auth::instance()->logged_in('admin') OR Auth::instance()->logged_in('edit')))
			{
				//アクティブのみを選択
				$sql->where('is_active', '=', 1);
			}

			// Divisionsがある場合
			if ($divisions)
			{
				$sql->where_open();
				foreach ($divisions as $division)
				{
					$sql->or_where('divisions.segment', '=', $division);
				}
				$sql->where_close();
			}

			// Categoriesがある場合
			if ($categories)
			{
				$sql->where_open();
				foreach ($categories as $category)
				{
					$sql->or_where('categories.segment', '=', $category);
				}
				$sql->where_close();
			}

			// Tagsがある場合
			if ($tags)
			{
				$sql->where_open();
				foreach ($tags as $tag)
				{
					$sql->or_where('tags.segment', '=', $tag);
				}
				$sql->where_close();
			}

			// AND検索のとき
			if ($and_or == 'and')
			{
				$sql->where_open();
				foreach ($strings as $string)
				{
					$sql->and_where(DB::expr("concat(items.segment, ' ', items.title, ' ', items.catch, ' ', items.keywords, ' ', items.description, ' ', items.summary)"), 'like', "%$string%");
				}
				$sql->where_close();
			}
			// OR検索のとき
			else
			{
				$sql->where_open();
				foreach ($strings as $string)
				{
					$sql->or_where(DB::expr("concat(items.segment, ' ', items.title, ' ', items.catch, ' ', items.keywords, ' ', items.description, ' ', items.summary)"), 'like', "%$string%");
				}
				$sql->where_close();
			}

			$sql->group_by('items.id');

			if ($order)
			{
				$order = explode(',', str_replace(' ', '', $order));
				$sql->order_by($order[0], $order[1]);
			}

			$items = $sql->as_object()
					->execute()
					->as_array('segment');
		}
		else
		{
			// ストリングが無いときは0配列を戻す
			$items = array();
		}

		// count, xn
		$c = 0;
		foreach ($items as &$item)
		{
			// countの追加
			$item->count = ++$c;

			// xn
			if ($xn)
			{
				$item->xn_start = (($item->count % $xn) == 1) ? TRUE : FALSE;
				$item->xn_end = (($item->count % $xn) == 0) ? TRUE : FALSE;
			}
		}

		/**
		 * Get total items：トータルを追加
		 */
		$result->total = count($items);

		/**
		 * Paginate があるとき
		 */
		if ($paginate)
		{
			$items_per_page = $paginate ? explode(',', str_replace(' ', '', $paginate))[0] : NULL;
			$follow = $paginate ? explode(',', str_replace(' ', '', $paginate))[1] : NULL;

			// Paginate resultに入れる
			//items_per_pageとfollow(前後のリンクの数)もgetでおくる
			$result->pagenate = Pgn::factory(array(
						'total_items' => $result->total,
						'items_per_page' => $items_per_page,
						'follow' => $follow,
			));

			// Paginated items resultに入れる
			$result->items = array_slice($items, $result->pagenate->offset, $result->pagenate->items_per_page);
		}
		else
		{
			$result->items = $items;
		}

		// itemsが０じゃないとき
		if ($result->items)
		{
			foreach ($result->items as &$item)
			{
				// cms item の get item でそれぞれのitemを取得
				$item_details = Cms_Functions::get_item($item->segment, $images_flag, $fields_flag, $comments_flag);

				// itemとitem_detailsをマージ
				$item = (object) array_merge((array) $item, (array) $item_details);
			}
		}

		// Set return
		Session::instance()->set('search_result', $result);
	}

}
