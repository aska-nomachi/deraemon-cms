<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Item extends Controller {

	public $logged_in_user = NULL;

	/**
	 * Action index
	 */
	public function action_index()
	{

//		//---------------------------------------------------------------//
//		if (Kohana::$profiling === TRUE)
//		{
//			// Start a new benchmark
//			$benchmark = Profiler::start('check', __FUNCTION__);
//		}
//		//Do some stuff--------------------------------------------------//
//		try
//		{
//
//		}
//		catch (Exception $e)
//		{
//			echo Debug::vars($e);
//		}
//
//
//		//Do some stuff--------------------------------------------------//
//		if (isset($benchmark))
//		{
//			// Stop the benchmark
//			Profiler::stop($benchmark);
//		}
//		echo View::factory('profiler/stats');
//		//---------------------------------------------------------------//

		/**
		 * Get settings
		 */
		// <editor-fold defaultstate="collapsed" desc="Get settings">
		$settings = Cms_Helper::settings();
		// </editor-fold>

		/**
		 * Lang
		 */
		// <editor-fold defaultstate="collapsed" desc="Lang">
		I18n::lang($settings->lang);
		// </editor-fold>

		/**
		 * Get item：セグメントからURLを取得　間はなんでもOK　でもセグメントはユニーク
		 */
		// <editor-fold defaultstate="collapsed" desc="Get segment and item">
		// Get item
		$item = Cms_Functions::get_item($this->request->param('segment'), TRUE, TRUE, FALSE);

		// Check issued
		if (Date::formatted_time($item->issued, 'U') > time())
		{
			$item = FALSE;
		}

		// itemがないとき（false）は404へ飛ばす
		if (!$item) throw HTTP_Exception::factory(404);

		// </editor-fold>

		/**
		 * If login
		 */
		// <editor-fold defaultstate="collapsed" desc="If login">
		// If switch and post ログイン機能ONのときポストがあったら
		if ($settings->author_login_is_on AND $this->request->post('login'))
		{
			$this->login_result = Cms_Item::login($this->request->post());
		}
		// </editor-fold>

		/**
		 * login check：ログインのチェック
		 */
		// <editor-fold defaultstate="collapsed" desc="login check">
		// ログインのチェック
		if (Auth::instance()->logged_in())
		{
			// Get user from auth
			$get_user = Auth::instance()->get_user();

			// Build logged_in_user
			$this->logged_in_user = (object) array(
					'id' => $get_user->id,
					'email' => $get_user->email,
					'username' => $get_user->username,
					'logins' => $get_user->logins,
					'last_login' => $get_user->last_login,
					'ext' => $get_user->ext,
					'avatar' => FALSE,
					'detail' => FALSE,
					'role' => FALSE,
			);

			// Set logged in user avatar
			if (is_file('application/'.Cms_Helper::settings('image_dir').'/user/'.$get_user->username.'/avatar'.$get_user->ext))
			{
				$this->logged_in_user->avatar = (object) array(
						'path' => URL::site("imagefly", 'http').'/user/'.$get_user->username.'/',
						'file' => '/'.'avatar'.$get_user->ext,
				);
			}

			// Set logged in user detail
			$this->logged_in_user->detail = Tbl::factory('users_details')
				->join('details')->on('users_details.detail_id', '=', 'details.id')
				->select('users_details.*')
				->select('details.name')
				->select('details.segment')
				->where('users_details.user_id', '=', $get_user->id)
				->read()
				->as_array('segment');

			// Set logged in user role
			$this->logged_in_user->role = Tbl::factory('roles_users')
				->select('roles.*')
				->join('roles')->on('roles_users.role_id', '=', 'roles.id')
				->where('roles_users.user_id', '=', $get_user->id)
				->where('roles.name', '!=', 'login')
				->read('name');
		}
		// </editor-fold>

		/**
		 * Set global value
		 */
		// <editor-fold defaultstate="collapsed" desc="Set global value">
		// Get site details
		$site = array();
		$site_detail_string = explode("\n", $settings->site_details);

		if ($site_detail_string)
		{
			foreach ($site_detail_string as $value)
			{
				$array = explode(':', $value);
				$site[trim($array[0])] = trim($array[1]);
			}
		}

		// Build logged_in_user
		if ($this->logged_in_user)
		{
			$logged_in_user = clone $this->logged_in_user;
			unset($logged_in_user->password, $logged_in_user->reset_key);
		}
		else
		{
			$logged_in_user = $this->logged_in_user;
		}

		Tpl::set_global(array(
			'host' => URL::base(true),
			'media_dir' => URL::site('media', 'http').'/',
			'images_dir' => URL::site('media/images', 'http').'/',
			'imagefly' => URL::site('imagefly/item', 'http').'/',
			'css_dir' => URL::site('media/css', 'http').'/',
			'js_dir' => URL::site('media/js', 'http').'/',
			'icon_dir' => URL::site('media/icon', 'http').'/',
			'lang' => $settings->lang,
			'logged_in_user' => $logged_in_user,
			'time' => time(),
			'return' => PHP_EOL,
			'site_title' => $settings->site_title,
			'site_email_address' => $settings->site_email_address,
			'site' => $site,
		));
		// </editor-fold>

		/**
		 * If logout
		 */
		// <editor-fold defaultstate="collapsed" desc="If logout">
		// If query ここはログイン機能OFFでもログアウト
		if ($this->request->query('logout'))
		{
			Cms_Item::logout();
			$this->redirect();
		}
		// </editor-fold>

		/**
		 * If post register
		 */
		// <editor-fold defaultstate="collapsed" desc="register">
		// If switch and post レジスター機能ONのときポストがあったら
		if ($settings->author_register_is_on AND $this->request->post('register'))
		{
			Cms_Item::register($this->request->post());
		}
		// </editor-fold>

		/**
		 * If get activate
		 */
		// <editor-fold defaultstate="collapsed" desc="activate">
		// If switch and post レジスター機能ONでアクティベートONのときポストがあったら
		if ($settings->author_register_is_on AND $settings->author_register_activate_is_on AND $this->request->query('activate_key'))
		{
			Cms_Item::activate($this->request->query());
		}
		// </editor-fold>

		/**
		 * If post forgot
		 */
		// <editor-fold defaultstate="collapsed" desc="forgot">
		// If switch and post フォーガット機能ONのときポストがあったら
		if ($settings->author_password_forgot_is_on AND $this->request->post('forgot'))
		{
			Cms_Item::forgot($this->request->post());
		}
		// </editor-fold>

		/**
		 * If post reset
		 */
		// <editor-fold defaultstate="collapsed" desc="reset">
		if ($settings->author_password_forgot_is_on AND ( $this->request->post('reset') OR $this->request->query('reset_key')))
		{
			Cms_Item::reset($this->request->post(), $this->request->query());
		}
		// </editor-fold>

		/**
		 * If post resign
		 */
		// <editor-fold defaultstate="collapsed" desc="resign">
		// If switch and post レジスター機能ONでアクティベートONのときポストがあったら
		if ($settings->author_register_is_on AND $settings->author_register_activate_is_on AND $this->request->post('resign'))
		{
			Cms_Item::resign($this->request->post());
		}
		// </editor-fold>

		/**
		 * If post account
		 */
		// <editor-fold defaultstate="collapsed" desc="account">
		if ($settings->author_account_is_on AND $this->request->post('account') AND $this->logged_in_user)
		{
			Cms_Item::account($this->request->post());
		}
		// </editor-fold>

		/**
		 * If post password
		 */
		// <editor-fold defaultstate="collapsed" desc="password">
		if ($settings->author_password_is_on AND $this->request->post('password') AND $this->logged_in_user)
		{
			Cms_Item::password($this->request->post());
		}
		// </editor-fold>

		/**
		 * If post detail
		 */
		// <editor-fold defaultstate="collapsed" desc="detail">
		if ($settings->author_detail_is_on AND $this->request->post('detail') AND $this->logged_in_user)
		{
			Cms_Item::detail($this->request->post());
		}
		// </editor-fold>

		/**
		 * If post send email
		 */
		// <editor-fold defaultstate="collapsed" desc="If post send email">
		// If switch and post
		if ($settings->send_email_is_on AND $this->request->post('send_email'))
		{
			Cms_Item::send_email($this->request->post());
		}
		// </editor-fold>

		/**
		 * If post send comment
		 */
		// <editor-fold defaultstate="collapsed" desc="If post send comment">
		// settingsのsend_comment_is_onと、itemのsend_comment_is_onが両方オンでポストsend_commentがあるとき
		$this->send_comment_result = new stdClass();

		if ($this->request->post('send_comment'))
		{
			if ($settings->send_comment_is_on AND $item->send_comment_is_on)
			{
				// send comment is user only
				// ユーザーだけ送信できる場合
				if ($settings->send_comment_is_user_only)
				{
					if ($this->logged_in_user)
					{
						$this->send_comment_result = Cms_Item::send_comment($item->id, $this->request->post());
					}
					else
					{
						$this->send_comment_result->information = TRUE;
						$this->send_comment_result->errors[] = array('field' => 'Only a user can comment. Please register as a user.');
					}
				}
				// だれでも送信できる場合
				else
				{
					$this->send_comment_result = Cms_Item::send_comment($item->id, $this->request->post());
				}
			}
			else
			{
				$this->send_comment_result->information = TRUE;
				$this->send_comment_result->errors[] = array('field' => 'The comment is not set up.');
			}
		}
		// </editor-fold>

		/**
		 * If get search
		 */
		// <editor-fold defaultstate="collapsed" desc="If get search">
		if ($this->request->query('search'))
		{
			Cms_Item::search($this->request->query());
		}
		// </editor-fold>

		/**
		 * Set ticket
		 *
		 * postにワンタイムチケットを使うときは{{&ticket}}をフォームの中に入れる
		 */
		// <editor-fold defaultstate="collapsed" desc="Set ticket">
		$ticket = Text::random('alnum', 8);
		Session::instance()->set('ticket', $ticket);
		Tpl::set_global(array(
			'ticket' => '<input type="hidden" name="ticket" value="'.$ticket.'" />',
		));
		// </editor-fold>

		/**
		 * First view render
		 */
		// <editor-fold defaultstate="collapsed" desc="First view render">
		
		// Itemをすべてarrayに変換		
		$first_html = Cms_Item::build_html($item);
		$first_view = Tpl::factory($first_html, array('item' => json_decode(json_encode($item), true)))
			->set('login_result', Session::instance()->get('login_result'))
			->set('logout_result', Session::instance()->get('logout_result'))
			->set('register_result', Session::instance()->get('register_result'))
			->set('activate_result', Session::instance()->get('activate_result'))
			->set('forgot_result', Session::instance()->get('forgot_result'))
			->set('reset_result', Session::instance()->get('reset_result'))
			->set('resign_result', Session::instance()->get('resign_result'))
			->set('detail_result', Session::instance()->get('detail_result'))
			->set('account_result', Session::instance()->get('account_result'))
			->set('password_result', Session::instance()->get('password_result'))
			->set('send_email_result', Session::instance()->get('send_email_result'))
			->set('send_comment_result', Session::instance()->get('send_comment_result'))
			->set('search_result', Session::instance()->get('search_result'))
		;
		// </editor-fold>

		/**
		 * Second view render
		 */
		// <editor-fold defaultstate="collapsed" desc="Second view render">
		$second_html = $first_view->render();
		$second_view = Tpl::factory($second_html, array('item' => $item));
		$html = $second_view->render();

		// delete result session 2階読み込むからget_onecじゃなくてここで消す。
		Session::instance()->delete('login_result');
		Session::instance()->delete('logout_result');
		Session::instance()->delete('register_result');
		Session::instance()->delete('activate_result');
		Session::instance()->delete('forgot_result');
		Session::instance()->delete('reset_result');
		Session::instance()->delete('resign_result');
		Session::instance()->delete('account_result');
		Session::instance()->delete('password_result');
		Session::instance()->delete('detail_result');
		Session::instance()->delete('send_email_result');
		Session::instance()->delete('send_comment_result');
		Session::instance()->delete('search_result');

		// </editor-fold>

		/**
		 * Response
		 */
		// <editor-fold defaultstate="collapsed" desc="Response">
		$this->response
			->headers('Content-Type', $item->wrapper->content_type);

		//Todo::1 ブラウザーキャッシュOK でもlogoutのときクリアできない！
		//// Browser cache
		//$this->response
		//	->headers('Cache-Control', 'max-age='.Date::HOUR.', public, must-revalidate')
		//	->headers('Expires', gmdate('D, d M Y H:i:s', time() + Date::HOUR).' GMT')
		//	->headers('ETag', $html);
		//// Tell browser to check the cache
		//$this->check_cache(sha1($html));
		//for jakartaekidan
		if ($item->wrapper->content_type == 'application/octet-stream')
		{
			$html = mb_convert_encoding($html, "SJIS", "UTF-8");
		}
		//for jakartaekidan

		$this->response->body($html);
		// </editor-fold>
	}

}
