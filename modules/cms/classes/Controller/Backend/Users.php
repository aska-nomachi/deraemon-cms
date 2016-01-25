<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Users extends Controller_Backend_Template {

	/**
	 * Before
	 */
	public function before()
	{
		parent::before();

		// Local menus
		$this->local_menus = array(
			'index' => array('name' => 'index', 'url' => URL::site("{$this->settings->backend_name}/users", 'http')),
			'edit' => array('name' => 'edit', 'url' => URL::site("{$this->settings->backend_name}/users/edit/{$this->request->param('key')}", 'http')),
			'detail' => array('name' => 'detail', 'url' => URL::site("{$this->settings->backend_name}/users/detail/{$this->request->param('key')}", 'http')),
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
		/*
		 * build column
		 */
		// <editor-fold defaultstate="collapsed" desc="column">
		// Get order
		$query = $this->request->query();
		$order_column = Arr::get($query, 'order_column', 'id');
		$order_direction = Arr::get($query, 'order_direction', 'ASC');

		// Build columns
		$columns = array(
			array(
				'name' => 'id',
				'order_column' => 'id',
				'order_direction' => 'ASC',
			),
			array(
				'name' => 'role',
				'order_column' => 'role',
				'order_direction' => 'ASC',
			),
			array(
				'name' => 'username',
				'order_column' => 'username',
				'order_direction' => 'ASC',
			),
			array(
				'name' => 'email',
				'order_column' => 'email',
				'order_direction' => 'ASC',
			),
			array(
				'name' => 'is block',
				'order_column' => 'is_block',
				'order_direction' => 'ASC',
			),
			array(
				'name' => '',
			),
		);

		foreach ($columns as &$column)
		{
			if (isset($column['order_column']))
			{
				if ($column['order_column'] == $order_column)
				{
					$column['current'] = TRUE;

					if ($order_direction == 'ASC')
					{
						$column['order_direction'] = 'DESC';
						$column['current_asc'] = TRUE;
					}
					else
					{
						$column['order_direction'] = 'ASC';
						$column['current_desc'] = TRUE;
					}
				}

				$column['url'] = URL::base(TRUE).Request::current()->uri().URL::query(array('order_column' => $column['order_column'], 'order_direction' => $column['order_direction']), FALSE);
			}
		}
		// </editor-fold>

		/*
		 * If post create
		 */
		// <editor-fold defaultstate="collapsed" desc="If post create">
		$create = array();

		// If there are post create
		if ($this->request->post('create'))
		{
			// Build data
			$create['username'] = $this->request->post('create_username');
			$create['email'] = $this->request->post('create_email');
			$create['password'] = $this->request->post('create_password');
			$create['avatar'] = $_FILES['create_avatar'];
			$create['is_block'] = $this->request->post('create_is_block') ? : 0;

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				// If there is not avatar アバターがない時
				if (!Upload::not_empty($create['avatar']))
				{
					$user = Tbl::factory('users')
						->create($create)
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
					Cms_Helper::make_dir($user->username, $this->settings->image_dir.'/user');
				}
				else
				{
					// Get image type
					$create['ext'] = NULL;
					switch ($create['avatar']['type'])
					{
						case 'image/jpeg':
							$create['ext'] = '.jpg';
							break;

						case 'image/png':
							$create['ext'] = '.png';
							break;

						case 'image/gif':
							$create['ext'] = '.gif';
							break;

						default:
							$create['ext'] = NULL;
							break;
					}

					// Create
					$user = Tbl::factory('users')
						->create($create, 'validate_with_avatar')
						->add_roles('login');

					// Make user dir
					Cms_Helper::make_dir($user->username, $this->settings->image_dir.'/user');

					// Image division directory // イメージを入れるディレクトリ
					$dir_path = 'application/'.$this->settings->image_dir.'/user/'.$user->username.'/';
					// Upload image イメージをアップロード
					$filename = Upload::save($create['avatar'], 'avatar'.$user->ext, $dir_path);

					// Build sizes
					$sizes = array(
						'_v' => explode(',', str_replace(' ', '', $this->settings->image_v)),
						'_h' => explode(',', str_replace(' ', '', $this->settings->image_h)),
						'_s' => explode(',', str_replace(' ', '', $this->settings->image_s)),
					);

					// Resize image 他のサイズを作成
					foreach ($sizes as $key => $value)
					{
						Image::factory($filename)
							->resize($value[0], $value[1], Image::INVERSE)
							->crop($value[0], $value[1])
							->save($dir_path.'avatar'.$key.$user->ext);
					}
				}

				// Database commit
				Database::instance()->commit();

				// Clear create
				$create['username'] = NULL;
				$create['email'] = NULL;
				$create['password'] = NULL;
				$create['thumb'] = NULL;
				$create['is_block'] = NULL;

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
					Notice::ERROR, $e->getMessage(), NULL, array($e->getFile(), $e->getLine())
				);
			}
		}
		// </editor-fold>

		/*
		 * Get users
		 */
		// <editor-fold defaultstate="collapsed" desc="Get users">		
		// Get users
		$all_users = Tbl::factory('users')
			->read()
			->as_array();

		foreach ($all_users as $all_user)
		{
			// Get user role
			$role = Tbl::factory('roles_users')
				->select('roles.*')
				->join('roles')->on('roles_users.role_id', '=', 'roles.id')
				->where('roles_users.user_id', '=', $all_user->id)
				->where('roles.name', '!=', 'login')
				->read('name');

			$all_user->role = $role ? : 'login';

			// Get avatar
			$all_user->avatar = new stdClass();
			$all_user->avatar->path = URL::site("imagefly", 'http').'/user/'.$all_user->username.'/';
			$all_user->avatar->file = '/'.'avatar'.$all_user->ext;

			if (!is_file('application/'.$this->settings->image_dir.'/user/'.$all_user->username.'/'.'avatar'.$all_user->ext))
			{
				$all_user->avatar = FALSE;
			}
		}

		// sort
		foreach ($all_users as $key => $value)
		{
			$key_id[$key] = $value->$order_column;
		}
		$sort = $order_direction == 'ASC' ? SORT_ASC : SORT_DESC;
		array_multisort($key_id, $sort, $all_users);

		/*
		 * Pagenate
		 */
		// <editor-fold defaultstate="collapsed" desc="Pagenate">
		$pagenate = Pgn::factory(array(
				'total_items' => count($all_users),
				'items_per_page' => $this->settings->pagenate_items_per_page_for_users,
				'follow' => $this->settings->pagenate_items_follow_for_users,
		));

		// Paginated items
		$users = array_slice($all_users, $pagenate->offset, $pagenate->items_per_page);
		// </editor-fold>
		// add edit
		foreach ($users as $user)
		{
			$user->edit_url = URL::site("{$this->settings->backend_name}/users/edit/{$user->id}", 'http');
		}
		// </editor-fold>

		/**
		 * If post update
		 */
		// <editor-fold defaultstate="collapsed" desc="If post update">
		if ($this->request->post('update'))
		{
			$post = $this->request->post();

			// Set post to user
			foreach ($users as $user)
			{
				$user->is_block = isset($post['is_block'][$user->id]) ? : 0;
			}

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				// Update
				foreach ($users as $user)
				{
					Tbl::factory('users')
						->get($user->id)
						->update(array(
							'is_block' => isset($post['is_block'][$user->id]) ? : 0,
					));
				}

				// Database commit
				Database::instance()->commit();

				// Add success notice
				Notice::add(Notice::SUCCESS, Kohana::message('general', 'update_success'));

				// Redirect to wrapper edit
				$this->redirect(URL::site("{$this->settings->backend_name}/users", 'http').URL::query());
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
		// </editor-fold>

		/**
		 * View
		 */
		// <editor-fold defaultstate="collapsed" desc="View">
		// local_menusの修正
		$this->local_menus = array(
			$this->local_menus['index']
		);

		$this->partials['pagenate'] = Tpl::get_file('pagenate', $this->settings->back_tpl_dir);

		$content_file = Tpl::get_file('index', $this->settings->back_tpl_dir.'/users', $this->partials);

		$this->content = Tpl::factory($content_file)
			->set('columns', $columns)
			->set('users', $users)
			->set('pagenate', $pagenate)
			->set('create', $create);
		// </editor-fold>
	}

	/**
	 * Action edit
	 */
	public function action_edit()
	{
		// Get id from param, if there is nothing then throw to 404
		$id = $this->request->param('key');
		if (!$id) throw HTTP_Exception::factory(404);

		// Get user, if there is nothing then throw to 404
		$user = Tbl::factory('users')->get($id);
		if (!$user) throw HTTP_Exception::factory(404);

		// Get user role
		$user->role = Tbl::factory('roles_users')
			->select('roles.*')
			->join('roles')->on('roles_users.role_id', '=', 'roles.id')
			->where('roles_users.user_id', '=', $user->id)
			->where('roles.name', '!=', 'login')
			->read('name');

		$user->avatar_delete_url = URL::site("{$this->settings->backend_name}/users/avatar_delete/{$user->id}", 'http');
		$user->delete_url = URL::site("{$this->settings->backend_name}/users/delete/{$user->id}", 'http');

		$user->avatar = new stdClass();
		$user->avatar->path = URL::site("imagefly", 'http').'/user/'.$user->username.'/';
		$user->avatar->file = '/'.'avatar'.$user->ext;

		if (!is_file('application/'.$this->settings->image_dir.'/user/'.$user->username.'/avatar'.$user->ext))
		{
			$user->avatar = FALSE;
		}

		// Get roles ラジオボタンのため
		$roles = Tbl::factory('roles')
			->where('roles.name', '!=', 'login')
			->read()
			->as_array();

		// Save old file
		$oldname = $user->username;

		// Build post
		$post = array(
			'username' => $user->username,
			'email' => $user->email,
			'role' => $user->role,
			'is_block' => $user->is_block ? : 0,
		);

		// If there are post
		if ($this->request->post())
		{
			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				// Get roles users id
				$roles_users_id = Tbl::factory('roles_users')
					->select('roles_users.*')
					->join('roles')->on('roles_users.role_id', '=', 'roles.id')
					->where('roles_users.user_id', '=', $user->id)
					->where('roles.name', '!=', 'login')
					->read('id');

				// if there is roles users id then delete 一回消してあとで入れなおす
				if ($roles_users_id)
				{
					Tbl::factory('roles_users')
						->where('id', '=', $roles_users_id)
						->get()
						->delete();
				}

				// Set post
				$post['username'] = $this->request->post('username');
				$post['email'] = $this->request->post('email');
				$post['role'] = $this->request->post('role');
				$post['is_block'] = $this->request->post('is_block') ? : 0;

				// Build data
				$data = array(
					'username' => $post['username'],
					'email' => $post['email'],
					'is_block' => $post['is_block'],
				);

				// If there is password
				if ($this->request->post('password'))
				{
					$data['password'] = $this->request->post('password');
				}

				/*
				 * If there is not avatar アバターがない時
				 */
				if (!Upload::not_empty($_FILES['avatar']))
				{
					// Update
					$user = Tbl::factory('users')
						->get($user->id)
						->update($data)
						->add_roles($post['role']);

					// New name
					$newname = $user->username;

					// Rename image user dir
					Cms_Helper::rename_dir($oldname, $newname, $this->settings->image_dir.'/user');
				}
				else
				{
					// Set post
					$data['avatar'] = $_FILES['avatar'];

					// Get image type
					$data['ext'] = NULL;
					switch ($data['avatar']['type'])
					{
						case 'image/jpeg':
							$data['ext'] = '.jpg';
							break;

						case 'image/png':
							$data['ext'] = '.png';
							break;

						case 'image/gif':
							$data['ext'] = '.gif';
							break;

						default:
							$data['ext'] = NULL;
							break;
					}

					// Update
					$user = Tbl::factory('users')
						->get($user->id)
						->update($data, 'validate_with_avatar')
						->add_roles($post['role']);

					// New name
					$newname = $user->username;

					// Rename image user dir
					Cms_Helper::rename_dir($oldname, $newname, $this->settings->image_dir.'/user');

					// Image division directory // イメージを入れるディレクトリ
					$dir_path = 'application/'.$this->settings->image_dir.'/user/'.$user->username.'/';
					// Upload image イメージをアップロード
					$filename = Upload::save($data['avatar'], 'avatar'.$user->ext, $dir_path);

					// Build sizes
					$sizes = array(
						'_v' => explode(',', str_replace(' ', '', $this->settings->image_v)),
						'_h' => explode(',', str_replace(' ', '', $this->settings->image_h)),
						'_s' => explode(',', str_replace(' ', '', $this->settings->image_s)),
					);

					// Resize image 他のサイズを作成
					foreach ($sizes as $key => $value)
					{
						Image::factory($filename)
							->resize($value[0], $value[1], Image::INVERSE)
							->crop($value[0], $value[1])
							->save($dir_path.'avatar'.$key.$user->ext);
					}
				}

				// reload
				$user->role = Tbl::factory('roles_users')
					->select('roles.*')
					->join('roles')->on('roles_users.role_id', '=', 'roles.id')
					->where('roles_users.user_id', '=', $user->id)
					->where('roles.name', '!=', 'login')
					->read('name');
				$user->avatar_delete_url = URL::site("{$this->settings->backend_name}/users/avatar_delete/{$user->id}", 'http');
				$user->delete_url = URL::site("{$this->settings->backend_name}/users/delete/{$user->id}", 'http');

				$user->avatar = new stdClass();
				$user->avatar->path = URL::site("imagefly", 'http').'/user/'.$user->username.'/';
				$user->avatar->file = '/'.'avatar'.$user->ext;

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
					Notice::ERROR, $e->getMessage().'/'.$e->getFile().'/'.$e->getLine()
				);
			}
		}

		/**
		 * View
		 */
		$content_file = Tpl::get_file('edit', $this->settings->back_tpl_dir.'/users', $this->partials);

		$this->content = Tpl::factory($content_file)
			->set('user', $user)
			->set('roles', $roles)
			->set('post', $post);
	}

	/**
	 * Action avatar delete
	 */
	public function action_avatar_delete()
	{
		// Auto render off
		$this->auto_render = FALSE;

		// Get id from param, if there is nothing then throw to 404
		$id = $this->request->param('key');
		if (!$id) throw HTTP_Exception::factory(404);

		// Get user, if there is nothing then throw to 404
		$user = Tbl::factory('users')->get($id);
		if (!$user) throw HTTP_Exception::factory(404);

		/**
		 * Delete
		 */
		// Database transaction start
		Database::instance()->begin();

		// Try
		try
		{
			// Get directory
			$dir_path = 'application/'.$this->settings->image_dir.'/user/'.$user->username.'/';

			// Delete image files
			if (is_file($dir_path.'avatar'.$user->ext))
			{
				unlink($dir_path.'avatar'.$user->ext);
				unlink($dir_path.'avatar'.'_v'.$user->ext);
				unlink($dir_path.'avatar'.'_h'.$user->ext);
				unlink($dir_path.'avatar'.'_s'.$user->ext);
			}

			// Update
			$user->update(array('ext' => NULL));

			// Database commit
			Database::instance()->commit();

			// Add success notice
			Notice::add(Notice::SUCCESS, Kohana::message('general', 'delete_success'));
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
				Notice::ERROR, $e->getMessage(), NULL, array($e->getFile(), $e->getLine())
			);
		}

		// Redirect to wrapper edit
		$this->redirect(URL::site("{$this->settings->backend_name}/users/edit/{$user->id}", 'http'));
	}

	/**
	 * Action details
	 */
	public function action_detail()
	{
		// Get id from param, if there is nothing then throw to 404
		$id = $this->request->param('key');
		if (!$id) throw HTTP_Exception::factory(404);

		// Get user, if there is nothing then throw to 404
		$user = Tbl::factory('users')->get($id);
		if (!$user) throw HTTP_Exception::factory(404);

		/**
		 * Get and build details
		 */
		// <editor-fold defaultstate="collapsed" desc="Get and build details">
		$details = Tbl::factory('users_details')
			->select('users_details.*')
			->select(array('details.segment', 'segment'))
			->select(array('details.name', 'name'))
			->join('details')->on('users_details.detail_id', '=', 'details.id')
			->where('users_details.user_id', '=', $user->id)
			->order_by('order')
			->read()
			->as_array('segment');
		// </editor-fold>

		/**
		 * If there are post
		 */
		if ($this->request->post())
		{
			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				// Get rules
				$rules = Tbl::factory('detail_rules')
					->join('details')->on('detail_rules.detail_id', '=', 'details.id')
					->read()
					->as_array();

				// Set post to details
				foreach ($this->request->post() as $key => $value)
				{
					if ($key != 'update')
					{
						$details[$key]->value = $value;
					}
				}

				/*
				 * validation
				 */
				// <editor-fold defaultstate="collapsed" desc="validation">
				// convert to array for validation
				$posts = array();
				foreach ($details as $key => $detail)
				{
					$posts[$key] = $detail->value;
				}

				// Validation factory
				$validation = Validation::factory($posts);

				// Iterate post set rule and label
				foreach ($rules as $rule)
				{
					// if param is null or 0 or ''
					if (!$rule->param)
					{
						$rule->param = NULL;
					}
					elseif (strpos($rule->param, ',') === FALSE)
					{
						$rule->param = array(trim($rule->param));
					}
					else
					{
						$rule->param = array(
							$param[] = trim(substr($rule->param, 0, strpos($rule->param, ','))),
							$param[] = trim(substr($rule->param, strpos($rule->param, ',') + 1)),
						);
					}

					$validation
						->rule($rule->segment, $rule->callback, $rule->param)
						->label($rule->segment, __($rule->name));
				}

				// If validation check is false
				if (!$validation->check()) throw new Validation_Exception($validation);
				// </editor-fold>

				foreach ($details as $detail)
				{
					Tbl::factory('users_details')
						->get($detail->id)
						->update(array('value' => $detail->value));
				}

				/**
				 * Get and build details
				 */
				// <editor-fold defaultstate="collapsed" desc="Get and build details">
				$details = Tbl::factory('users_details')
					->select('users_details.*')
					->select(array('details.segment', 'segment'))
					->select(array('details.name', 'name'))
					->join('details')->on('users_details.detail_id', '=', 'details.id')
					->where('users_details.user_id', '=', $user->id)
					->order_by('order')
					->read()
					->as_array('segment');
				// </editor-fold>

				/**
				 * Database commit
				 */
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
					Notice::ERROR, $e->getMessage().'/'.$e->getFile().'/'.$e->getLine()
				);
			}
		}

		/**
		 * View
		 */
		$content_file = Tpl::get_file('detail', $this->settings->back_tpl_dir.'/users', $this->partials);

		$this->content = Tpl::factory($content_file)
			->set('user', $user)
			->set('details', $details);
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
		if (!$id) throw HTTP_Exception::factory(404);

		// Get tag, if there is nothing then throw to 404
		$user = Tbl::factory('users')->get($id);
		if (!$user) throw HTTP_Exception::factory(404);

		/**
		 * Delete
		 */
		// Database transaction start
		Database::instance()->begin();

		// Try
		try
		{
			// Delete roles_users
			$roles_users_ids = Tbl::factory('roles_users')
				->where('user_id', '=', $user->id)
				->read()
				->as_array(NULL, 'id');

			if ($roles_users_ids)
			{
				foreach ($roles_users_ids as $roles_users_id)
				{
					Tbl::factory('roles_users')
						->get($roles_users_id)
						->delete();
				}
			}

			// Delate users_details
			$users_details_ids = Tbl::factory('users_details')
				->where('user_id', '=', $user->id)
				->read()
				->as_array(NULL, 'id');

			if ($users_details_ids)
			{
				foreach ($users_details_ids as $users_details_id)
				{
					Tbl::factory('users_details')
						->get($users_details_id)
						->delete();
				}
			}

			// Delete
			$user->delete();

			// Delete image user dir
			Cms_Helper::delete_dir($user->username, $this->settings->image_dir.'/user', TRUE);

			// Database commit
			Database::instance()->commit();

			// Add success notice
			Notice::add(Notice::SUCCESS, Kohana::message('general', 'delete_success'));
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
				Notice::ERROR, $e->getMessage()
			);
		}

		// Redirect to wrapper edit
		$this->redirect(URL::site("{$this->settings->backend_name}/users", 'http'));
	}

	/**
	 * After
	 */
	public function after()
	{
		parent::after();
	}

}
