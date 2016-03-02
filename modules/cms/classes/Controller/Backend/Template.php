<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Template extends Controller {

	// Todo::2
	// 新しいテーマに関係するのは、 wrappers divisions shapes parts emails errorsのファイルを持つもの。
	// データベースにも入ってるからテーマチェンジはできない、、、方法を検討。
	// 検討結果↓
	// wrappers divisionsを限定
	// wrappers -> html
	// divisions -> pageとpost最初はこれだけ！

	public $template;
	public $auto_render = TRUE;
	public $settings = NULL;
	public $logged_in_user = NULL;
	public $menu = NULL;
	public $partials = array();
	public $content = NULL;
	public $local_menus = array();
	public $frontend_link = array();
	public $snippets = array();

	/**
	 * Before
	 */
	public function before()
	{
		/**
		 * before
		 */
		parent::before();

		/**
		 * Get settings
		 */
		// <editor-fold defaultstate="collapsed" desc="Get settings">
		$this->settings = Cms_Helper::settings();
		// </editor-fold>

		/**
		 * Set website language
		 */
		// <editor-fold defaultstate="collapsed" desc="Set website language">
		I18n::lang('backend'.$this->settings->backend_lang);
		// </editor-fold>

		/**
		 * Authenticate and get logged in user：ディレクト、アドミン、エディター以外は入れない、入ったときはlogged in userをセット
		 */
		// <editor-fold defaultstate="collapsed" desc="Authenticate and get logged in user">
		if (Auth::instance()->logged_in('direct') OR Auth::instance()->logged_in('admin') OR Auth::instance()->logged_in('edit'))
		{
			// もしログインしていてアクションがloginの場合はバックエンドホームに飛ばす
			if ($this->request->action() == 'login')
			{
				HTTP::redirect(URL::site($this->settings->backend_name, 'http'));
			}

			// Set logged in user
			$this->logged_in_user = Tbl::factory('users')
				->where('id', '=', Auth::instance()->get_user()->id)
				->read(1);

			// Set logged in user role
			$this->logged_in_user->role = Tbl::factory('roles_users')
				->select('roles.*')
				->join('roles')->on('roles_users.role_id', '=', 'roles.id')
				->where('roles_users.user_id', '=', $this->logged_in_user->id)
				->where('roles.name', '!=', 'login')
				->read('name');

			// Set logged in user role name for template
			$this->logged_in_user->{$this->logged_in_user->role} = TRUE;
		}
		else
		{
			// If not logged in throw to login
			if (!($this->request->controller() == 'Auth'))
			{
				$this->redirect(URL::site("{$this->settings->backend_name}/login", 'http'));
			}
		}
		// </editor-fold>

		/**
		 * Get item menu：itemのメニューを取得 $menusのitemsに入れる
		 */
		// <editor-fold defaultstate="collapsed" desc="Get item menu">
		$divisions = Tbl::factory('divisions')
			->read()
			->as_array();

		// itemのチルドレンの配列を作成
		$item_children = array();

		// item search
		// controllerは新しく作ったよ
		$item_children['item_search'] = array(
			'name' => 'item search',
			'controller' => 'item_search',
			'division' => '',
			'actions' => array('index'),
			'url' => URL::site("{$this->settings->backend_name}/item_search", 'http'),
			'roles' => array('direct', 'admin', 'edit'),
			'allow' => FALSE,
		);

		// 作成されたディビジョンの数だけ作成
		foreach ($divisions as $division)
		{
			$item_children[$division->name] = array(
				'name' => $division->name, // Todo:: ここは$division->nameのほうがよい？
				'controller' => 'items',
				'division' => $division->segment,
				'actions' => array('index', 'edit', 'content', 'images', 'image_delete', 'fields', 'received_comments', 'received_comment_delete', 'delete'),
				'url' => URL::site("{$this->settings->backend_name}/items/{$division->segment}", 'http'),
				'roles' => array('direct', 'admin', 'edit'),
				'allow' => FALSE,
			);
		}
		// </editor-fold>

		/**
		 * Build menu：全体のメニューを作成
		 *
		 * 許可するロールを指定
		 * 'roles' => array('direct', 'admin', 'edit')
		 */
		// <editor-fold defaultstate="collapsed" desc="Build menu">
		$this->menus = array(
			'dashboard' => array(
				'name' => 'dashboard',
				'icon' => 'fa fa-dashboard',
				'children' => array(
					'home' => array(
						'name' => 'home',
						'controller' => 'home',
						'actions' => array('index', 'about', 'syntax'),//, 'function', 'tutorial'
						'url' => URL::site("{$this->settings->backend_name}/home/index", 'http'),
						'allow' => FALSE,
						'roles' => array('direct', 'admin', 'edit'),
					),
					'settings' => array(
						'name' => 'settings',
						'controller' => 'settings',
						'actions' => array('index', 'frontend', 'backend', 'paginate', 'image', 'email', 'comment', 'auth', 'other'),
						'url' => URL::site("{$this->settings->backend_name}/settings/index", 'http'),
						'allow' => FALSE,
						'roles' => array('direct'),
					),
					'users' => array(
						'name' => 'users',
						'controller' => 'users',
						'actions' => array('index', 'edit', 'avatar_delete', 'detail', 'delete'),
						'url' => URL::site("{$this->settings->backend_name}/users/index", 'http'),
						'allow' => FALSE,
						'roles' => array('direct', 'admin'),
					),
					'received_emails' => array(
						'name' => 'received emails',
						'controller' => 'received_emails',
						'actions' => array('index', 'delete'),
						'url' => URL::site("{$this->settings->backend_name}/received-emails/index", 'http'),
						'allow' => FALSE,
						'roles' => array('direct', 'admin'),
					),
					'received_comments' => array(
						'name' => 'received comments',
						'controller' => 'received_comments',
						'actions' => array('index', 'delete'),
						'url' => URL::site("{$this->settings->backend_name}/received-comments/index", 'http'),
						'allow' => FALSE,
						'roles' => array('direct', 'admin'),
					),
				),
			),
			'template' => array(
				'name' => 'template',
				'icon' => 'fa fa-file-o',
				'children' => array(
					'wrappers' => array(
						'name' => 'wrappers',
						'controller' => 'wrappers',
						'actions' => array('index', 'edit', 'delete'),
						'url' => URL::site("{$this->settings->backend_name}/wrappers/index", 'http'),
						'allow' => FALSE,
						'roles' => array('direct', 'admin'),
					),
					'divisions' => array(
						'name' => 'divisions',
						'controller' => 'divisions',
						'actions' => array('index', 'edit', 'delete'),
						'url' => URL::site("{$this->settings->backend_name}/divisions/index", 'http'),
						'allow' => FALSE,
						'roles' => array('direct', 'admin'),
					),
					'shapes' => array(
						'name' => 'shapes',
						'controller' => 'shapes',
						'actions' => array('index', 'edit', 'delete'),
						'url' => URL::site("{$this->settings->backend_name}/shapes/index", 'http'),
						'allow' => FALSE,
						'roles' => array('direct', 'admin'),
					),
					'parts' => array(
						'name' => 'parts',
						'controller' => 'parts',
						'actions' => array('index', 'edit', 'delete'),
						'url' => URL::site("{$this->settings->backend_name}/parts/index", 'http'),
						'allow' => FALSE,
						'roles' => array('direct', 'admin'),
					),
					'search' => array(
						'name' => 'search',
						'controller' => 'search',
						'actions' => array('index', 'form', 'result', 'delete'),
						'url' => URL::site("{$this->settings->backend_name}/search/form", 'http'),
						'allow' => FALSE,
						'roles' => array('direct', 'admin'),
					),
					'emails' => array(
						'name' => 'emails',
						'controller' => 'emails',
						'actions' => array('index', 'edit', 'confirm', 'receive', 'rule', 'rule_delete', 'delete'),
						'url' => URL::site("{$this->settings->backend_name}/emails", 'http'),
						'allow' => FALSE,
						'roles' => array('direct', 'admin'),
					),
					'comment' => array(
						'name' => 'comment',
						'controller' => 'comment',
						'actions' => array('index', 'form', 'result'),
						'url' => URL::site("{$this->settings->backend_name}/comment", 'http'),
						'allow' => FALSE,
						'roles' => array('direct', 'admin'),
					),
					'author' => array(
						'name' => 'author',
						'controller' => 'author',
						'actions' => array('index', 'login', 'register', 'activate_mail', 'activate', 'forgot', 'reset_mail', 'reset', 'resign', 'account', 'password', 'detail'),
						'url' => URL::site("{$this->settings->backend_name}/author", 'http'),
						'allow' => FALSE,
						'roles' => array('direct', 'admin'),
					),
					'errors' => array(
						'name' => 'errors',
						'controller' => 'errors',
						'actions' => array('index', '404', '500', 'default'),
						'url' => URL::site("{$this->settings->backend_name}/errors/404", 'http'),
						'allow' => FALSE,
						'roles' => array('direct', 'admin'),
					),
				),
			),
			'attachment' => array(
				'name' => 'attachment',
				'icon' => 'fa fa-paperclip',
				'children' => array(
					'fields' => array(
						'name' => 'fields',
						'controller' => 'fields',
						'actions' => array('index', 'edit', 'delete'),
						'url' => URL::site("{$this->settings->backend_name}/fields/index", 'http'),
						'allow' => FALSE,
						'roles' => array('direct', 'admin'),
					),
					'categories' => array(
						'name' => 'categories',
						'controller' => 'categories',
						'actions' => array('index', 'edit', 'delete'),
						'url' => URL::site("{$this->settings->backend_name}/categories/index", 'http'),
						'allow' => FALSE,
						'roles' => array('direct', 'admin'),
					),
					'tags' => array(
						'name' => 'tags',
						'controller' => 'tags',
						'actions' => array('index', 'edit', 'delete'),
						'url' => URL::site("{$this->settings->backend_name}/tags/index", 'http'),
						'allow' => FALSE,
						'roles' => array('direct', 'admin'),
					),
					'details' => array(
						'name' => 'details',
						'controller' => 'details',
						'actions' => array('index', 'edit', 'rule', 'rule_delete', 'delete'),
						'url' => URL::site("{$this->settings->backend_name}/details/index", 'http'),
						'allow' => FALSE,
						'roles' => array('direct', 'admin'),
					),
				),
			),
			'items' => array(
				'name' => 'items',
				'icon' => 'fa fa-files-o',
				'children' => $item_children,
			),
		);
		// </editor-fold>

		/**
		 * Set current to menu：currentをセット、現在のページ（current_child）をセット
		 */
		// <editor-fold defaultstate="collapsed" desc="Set current to menu">
		$controller = strtolower($this->request->controller());
		$action = strtolower($this->request->action());
		$division = $this->request->param('division');
		$current_child = NULL;

		// メニューをイテレート
		foreach ($this->menus as $key => &$value)
		{
			// メニューのチャイルドをイテレート
			foreach ($value['children'] as &$child)
			{
				// If except an item：もしcontrollerがitemsの時で↓
				if ($child['controller'] !== 'items')
				{
					// カレントの場合
					if ($child['controller'] === $controller)
					{
						// parrentにTRUEをセット
						$value['current'] = TRUE;

						if (in_array($action, $child['actions']))
						{
							// currentにTRUEをセット
							$child['current'] = TRUE;

							// カレントチャイルドにセット
							$current_child = (object) $child;
							$current_child->parrent = $key;
						}
					}
					// ロールがある場合
					if ($this->logged_in_user AND in_array($this->logged_in_user->role, $child['roles']))
					{
						$child['allow'] = TRUE;
					}
				}
				// If except an item：もしcontrollerがitems以外の時で↓
				else
				{
					// カレントの場合
					if ($child['division'] === $division)
					{
						// currentにTRUEをセット
						$value['current'] = TRUE;

						if (in_array($action, $child['actions']))
						{
							// currentにTRUEをセット
							$child['current'] = TRUE;

							// カレントチャイルドにセット
							$current_child = (object) $child;
							$current_child->parrent = $key;
						}
					}
					// ロールがある場合
					if ($this->logged_in_user AND in_array($this->logged_in_user->role, $child['roles']))
					{
						$child['allow'] = TRUE;
					}
				}
			}
		}
		// </editor-fold>

		/**
		 * Allow page controll：許可するページのみ入れる、それ以外はのNoticeを出してredirect
		 *
		 * user_idのfilterはitemsのcontlollerで行う
		 */
		// <editor-fold defaultstate="collapsed" desc="Allow page controll">
		if ($this->logged_in_user AND $current_child)
		{
			// ロールに含まれない場合
			if (!in_array($this->logged_in_user->role, $current_child->roles))
			{
				// ワーニング
				Notice::add(Notice::WARNING, Kohana::message('general', 'no_authority'));
				// リダイレクト
				$this->redirect(URL::site("{$this->settings->backend_name}", 'http'));
			}
		}
		// ログインは回避
		elseif($controller !== 'auth' AND $action !== 'login')
		{
			// ワーニング
			Notice::add(Notice::WARNING, Kohana::message('general', 'no_authority'));
			// リダイレクト
			$this->redirect(URL::site("{$this->settings->backend_name}", 'http'));
		}
		// </editor-fold>

		/**
		 * View
		 */
		// <editor-fold defaultstate="collapsed" desc="View">
		if ($this->auto_render)
		{
			// Partial header and footer
			$this->partials['header'] = Tpl::get_file('header', $this->settings->back_tpl_dir);
			$this->partials['footer'] = Tpl::get_file('footer', $this->settings->back_tpl_dir);
			$this->partials['snippets'] = Tpl::get_file('snippets', $this->settings->back_tpl_dir);
		}
		// </editor-fold>
	}

	/**
	 * After
	 */
	public function after()
	{
		// Auto render
		if ($this->auto_render)
		{
			/**
			 * build snippets -> snippetsようにつくるようにつくる！
			 */
			// <editor-fold defaultstate="collapsed" desc="build snippets">
			// Get site details
			$sites = array();
			$site_details = Tbl::factory('settings')
				->where('key', '=', 'site_details')
				->read('value');
			$site_detail_strings = explode("\n", $site_details);
			if ($site_detail_strings)
			{
				foreach ($site_detail_strings as $site_detail_string)
				{
					$array = explode(':', $site_detail_string);
					$sites[trim($array[0])] = array(
						'key' => trim($array[0]),
						'value' => trim($array[1]),
					);
				}
			}

			// Get items for snippets item. 下の$this->snippetsのitemに入れるように取得する
			$snippet_item = NULL;
			if ($this->request->param('key'))
			{
				$snippet_item_segment = Tbl::factory('items')
					->where('id', '=', $this->request->param('key'))
					->read('segment');

				if ($snippet_item_segment)
				{
					$snippet_item = Cms_Functions::get_item($snippet_item_segment, TRUE, TRUE, TRUE);
				}
			}

			// Get parts for snippets part. 下の$this->snippetsのpartsに入れるように取得する
			$snippet_parts = Cms_Helper::get_dirfiles('part', $this->settings->front_tpl_dir . $this->settings->front_theme);
			foreach ($snippet_parts as $snippet_part)
			{
				$snippet_part->content = Tpl::get_file($snippet_part->segment, $this->settings->front_tpl_dir . $this->settings->front_theme.'/part');
			}

			// Set snippets
			$this->snippets = array(
				'host' => URL::base(true),
				'media_dir' => URL::site('media', 'http').'/',
				'images_dir' => URL::site('media/images_dir', 'http').'/',
				'css_dir' => URL::site('media/css_dir', 'http').'/',
				'js_dir' => URL::site('media/js_dir', 'http').'/',
				'icon_dir' => URL::site('media/icon_dir', 'http').'/',
				'lang' => $this->settings->lang,
				'logged_in_user' => array(
					'id' => isset($this->logged_in_user->id) ? $this->logged_in_user->id : NULL,
					'email' => isset($this->logged_in_user->email) ? $this->logged_in_user->email : NULL,
					'username' => isset($this->logged_in_user->username) ? $this->logged_in_user->username : NULL,
					'logins' => isset($this->logged_in_user->logins) ? $this->logged_in_user->logins : NULL,
					'details' => isset($this->logged_in_user->details) ? $this->logged_in_user->details : NULL,
				),
				'sites' => $sites,
				'timestamp' => time(),
				'return' => 'PHP_EOL',
				'item' => isset($snippet_item) ? (object) $snippet_item : NULL,
				'parts' => isset($snippet_parts) ? (object) $snippet_parts : NULL,
			);
			// </editor-fold>

			/**
			 * View
			 */
			// <editor-fold defaultstate="collapsed" desc="View">
			// Set global value -> Set to contentといっしょ
			Tpl::set_global(array(
				'host' => URL::base(true),
				'site_title' => $this->settings->site_title,
				'site_email_address' => $this->settings->site_email_address,
				'backend_host' => URL::base(true).$this->settings->backend_name.'/',
				'logged_in_user' => $this->logged_in_user,
				'logout_url' => URL::site("{$this->settings->backend_name}/logout", 'http'),
				'time' => time(),
			));

			// Set to content
			$this->content
				->set('menus', $this->menus)
				->set('notice', Notice::render())
				->set('local_menus', $this->local_menus)
				->set('frontend_link', $this->frontend_link)
				->set('snippets', $this->snippets)
			;

			// Get tamplate file
			$template = Tpl::get_file('template', $this->settings->back_tpl_dir);
			$backend_ucfirst = str_replace('_', ' ', Text::ucfirst($this->settings->backend_name, '_'));

			// Factory and set
			$this->template = Tpl::factory($template)
				->set('title', $backend_ucfirst)
				->set('keywords', $backend_ucfirst)
				->set('description', $backend_ucfirst)
				->set('content', $this->content->render())
			;

			// Render body
			$this->response->body($this->template->render());

			// </editor-fold>
		}

		/**
		 * after
		 */
		parent::after();
	}

}
