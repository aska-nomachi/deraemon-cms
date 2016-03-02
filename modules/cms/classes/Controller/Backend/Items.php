<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Items extends Controller_Backend_Template {

	public $item = NULL;

	/**
	 * Before
	 */
	public function before()
	{
		parent::before();

		// イメージデリートのとき
		if ($this->request->action() == 'image_delete')
		{
			// logged in userのroleとuser_idをチェックして、editの場合user_idが一致しないと404
			if ($this->logged_in_user->role == 'edit' AND ! ($this->logged_in_user->id == $this->item->user_id))
			{
				throw HTTP_Exception::factory(404);
			}
		}
		// コメントデリートのとき
		elseif ($this->request->action() == 'received_comment_delete')
		{
			// logged in userのroleとuser_idをチェックして、editの場合user_idが一致しないと404
			if ($this->logged_in_user->role == 'edit' AND ! ($this->logged_in_user->id == $this->item->user_id))
			{
				throw HTTP_Exception::factory(404);
			}
		}
		// インデックスじゃないとき
		elseif ($this->request->action() != 'index')
		{
			// Get id from param, if there is nothing then throw to 404：これをbeforeでおこなう？
			$id = $this->request->param('key');
			if (!$id) throw HTTP_Exception::factory(404);

			// Get item, if there is nothing then throw to 404：これもbeforeでおこなう？
			$this->item = Tbl::factory('items')->get($id);
			if (!$this->item) throw HTTP_Exception::factory(404);

			// logged in userのroleとuser_idをチェックして、editの場合user_idが一致しないと404
			if ($this->logged_in_user->role == 'edit' AND ! ($this->logged_in_user->id == $this->item->user_id))
			{
				throw HTTP_Exception::factory(404);
			}

			$this->frontend_link = array('title' => $this->item->segment, 'url' => URL::base('http').$this->item->segment);
		}

		/**
		 * Local menu
		 */
		$this->local_menus = array(
			'index' => array('name' => 'index', 'url' => URL::site("{$this->settings->backend_name}/items/{$this->request->param('division')}", 'http')),
			'edit' => array('name' => 'edit', 'url' => URL::site("{$this->settings->backend_name}/items/{$this->request->param('division')}/edit/{$this->request->param('key')}", 'http')),
			'content' => array('name' => 'content', 'url' => URL::site("{$this->settings->backend_name}/items/{$this->request->param('division')}/content/{$this->request->param('key')}", 'http')),
			'images' => array('name' => 'images', 'url' => URL::site("{$this->settings->backend_name}/items/{$this->request->param('division')}/images/{$this->request->param('key')}", 'http')),
			'fields' => array('name' => 'fields', 'url' => URL::site("{$this->settings->backend_name}/items/{$this->request->param('division')}/fields/{$this->request->param('key')}", 'http')),
			'received_comments' => array('name' => 'received comments', 'url' => URL::site("{$this->settings->backend_name}/items/{$this->request->param('division')}/received-comments/{$this->request->param('key')}", 'http')),
		);

		// local menus set current
		foreach ($this->local_menus as $key => &$value)
		{
			if ($key == $this->request->action())
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
		// Get id from param, if there is nothing then throw to 404
		$division_segment = $this->request->param('division');
		if (!$division_segment) throw HTTP_Exception::factory(404);

		$division = Tbl::factory('divisions')
			->where('segment', '=', $division_segment)
			->read(1);
		if (!$division) throw HTTP_Exception::factory(404);

		/*
		 * Build columns
		 */
		// <editor-fold defaultstate="collapsed" desc="Build columns">
		// Get order
		$query = $this->request->query();
		$order_column = Arr::get($query, 'order_column', 'order');
		$order_direction = Arr::get($query, 'order_direction', 'ASC');

		$columns = array(
			'id' => array(
				'name' => 'id',
				'order_column' => 'id',
				'order_direction' => 'ASC',
			),
			'title' => array(
				'name' => 'title',
				'order_column' => 'title',
				'order_direction' => 'ASC',
			),
			'segment' => array(
				'name' => 'segment',
				'order_column' => 'segment',
				'order_direction' => 'ASC',
			),
			'username' => array(
				'name' => 'username',
				'order_column' => 'username',
				'order_direction' => 'ASC',
			),
			'issued' => array(
				'name' => 'issued',
				'order_column' => 'issued',
				'order_direction' => 'ASC',
			),
			'created' => array(
				'name' => 'created',
				'order_column' => 'created',
				'order_direction' => 'ASC',
			),
			'order' => array(
				'name' => 'order',
				'order_column' => 'order',
				'order_direction' => 'ASC',
			),
			'activate' => array(
				'name' => 'activate',
				'order_column' => 'is_active',
				'order_direction' => 'ASC',
			),
			'send_comment_is_on' => array(
				'name' => 'send_comment_is_on',
				'order_column' => 'send_comment_is_on',
				'order_direction' => 'ASC',
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

		if ($this->request->post('create'))
		{
			// Build data
			$create['division_id'] = $division->id;
			$create['image_id'] = NULL;
			$create['user_id'] = $this->logged_in_user->id;
			$create['segment'] = $this->request->post('create_segment');
			$create['title'] = $this->request->post('create_title');
			$create['issued'] = $this->request->post('create_issued') ? : Date::formatted_time();
			$create['created'] = Date::formatted_time();
			$create['send_comment_is_on'] = $this->settings->send_comment_is_on_default;

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				// Create
				$item = Tbl::factory('items')
					->create($create);

				// Create items_fields
				$field_ids = Tbl::factory('fields')
					->where('division_id', '=', $item->division_id)
					->read()
					->as_array(NULL, 'id');

				foreach ($field_ids as $field_id)
				{
					Tbl::factory('items_fields')
						->create(array(
							'item_id' => $item->id,
							'field_id' => $field_id,
							'value' => NULL,
					));
				}

				// Create file
				Cms_Helper::set_file($item->segment, $this->settings->item_dir.'/'.$division->segment, 'item content');

				// make image dir // images dirにitem segment名でディレクトリを作成
				Cms_Helper::make_dir($item->segment, $this->settings->image_dir.'/item/'.$division->segment);

				// Database commit
				Database::instance()->commit();

				// Clear create
				$create['segment'] = NULL;
				$create['title'] = NULL;
				$create['issued'] = NULL;

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
					Notice::ERROR//, $e->getMessage(), NULL, array($e->getFile(), $e->getLine())
				);
			}
		}
		// </editor-fold>

		/*
		 * Get items
		 */
		// <editor-fold defaultstate="collapsed" desc="Get items">
		// authority is edit
		if ($this->logged_in_user->role == 'edit')
		{
			// Get all items
			$all_items = Tbl::factory('items')
				->select('items.*')
				->select('users.username')
				->join('users', 'LEFT')->on('items.user_id', '=', 'users.id')
				->where('division_id', '=', $division->id)
				->where('users.id', '=', $this->logged_in_user->id)
				->order_by($order_column, $order_direction)
				->read()
				->as_array();
		}
		elseif ($this->logged_in_user->role == 'direct' OR $this->logged_in_user->role == 'admin')
		{
			// Get all items
			$all_items = Tbl::factory('items')
				->select('items.*')
				->select('users.username')
				->join('users', 'LEFT')->on('items.user_id', '=', 'users.id')
				->where('division_id', '=', $division->id)
				->order_by($order_column, $order_direction)
				->read()
				->as_array();
		}
		else
		{
			$all_items = array();
		}

		// paginate
		$paginate = Pgn::factory(array(
				'total_items' => count($all_items),
				'items_per_page' => $this->settings->paginate_items_per_page_for_items,
				'follow' => $this->settings->paginate_items_follow_for_items,
		));

		// Paginated items
		$items = array_slice($all_items, $paginate->offset, $paginate->items_per_page);

		foreach ($items as $item)
		{
			// Get division
			$division = Tbl::factory('divisions')
				->where('id', '=', $item->division_id)
				->read(1);

			// Get main image
			$item->main_image = Tbl::factory('images')
				->where('id', '=', $item->image_id)
				->read(1);

			if ($item->main_image)
			{
				$item->main_image->path = URL::site("imagefly", 'http').'/item/'.$division->segment.'/'.$item->segment.'/';
				$item->main_image->file = '/'.$item->main_image->segment.$item->main_image->ext;
			}

			// Get categories
			$item->categories = Tbl::factory('categories')
				->select('categories.*')
				->join('items_categories')->on('categories.id', '=', 'items_categories.category_id')
				->where('items_categories.item_id', '=', $item->id)
				->read()
				->as_array();

			// Get received comments
			$item->received_commnets_count = count(Tbl::factory('received_comments')
					->where('item_id', '=', $item->id)
					->read()
					->as_array(NULL, 'id'));

			// Set to item
			$item->issued = $item->issued ? Date::formatted_time($item->issued, 'Y-n-j h:i') : $item->issued;
			$item->created = $item->created ? Date::formatted_time($item->created, 'Y-n-j h:i') : $item->created;
			$item->summary = Text::auto_p($item->summary);
			$item->edit_url = URL::site("{$this->settings->backend_name}/items/{$division->segment}/edit/{$item->id}", 'http');
			$item->delete_url = URL::site("{$this->settings->backend_name}/items/{$division->segment}/delete/{$item->id}", 'http');
		}
		// </editor-fold>

		/**
		 * If post update
		 */
		// <editor-fold defaultstate="collapsed" desc="If post update">
		if ($this->request->post('update'))
		{
			$post = $this->request->post();

			// Set post to item
			foreach ($items as $item)
			{
				$item->order = isset($post['order'][$item->id]) ? $post['order'][$item->id] : 0;
				$item->is_active = isset($post['is_active'][$item->id]) ? $post['is_active'][$item->id] : 0;
				$item->send_comment_is_on = isset($post['send_comment_is_on'][$item->id]) ? $post['send_comment_is_on'][$item->id] : 0;
			}

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				// Update
				foreach ($items as $item)
				{
					Tbl::factory('items')
						->get($item->id)
						->update(array(
							'order' => isset($post['order'][$item->id]) ? $post['order'][$item->id] : 0,
							'is_active' => isset($post['is_active'][$item->id]) ? $post['is_active'][$item->id] : 0,
							'send_comment_is_on' => isset($post['send_comment_is_on'][$item->id]) ? $post['send_comment_is_on'][$item->id] : 0,
					));
				}

				// Database commit
				Database::instance()->commit();

				// Add success notice
				Notice::add(Notice::SUCCESS, Kohana::message('general', 'update_success'));

				// Todo::1 取得しなおし？
				$this->redirect(URL::site("{$this->settings->backend_name}/items/{$division->segment}", 'http'));
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
		// local_menusの修正
		$this->local_menus = array($this->local_menus['index']);

		/**
		 * View
		 */
		// <editor-fold defaultstate="collapsed" desc="View">
		$this->partials['paginate'] = Tpl::get_file('paginate', $this->settings->back_tpl_dir);

		$content_file = Tpl::get_file('index', $this->settings->back_tpl_dir.'/items', $this->partials);

		$this->content = Tpl::factory($content_file)
			->set('division', $division)
			->set('columns', $columns)
			->set('items', $items)
			->set('create', $create)
			->set('paginate', $paginate);
		// </editor-fold>
	}

	/**
	 * Action delete
	 */
	public function action_delete()
	{
		// Auto render off
		$this->auto_render = FALSE;

		// Get division
		$division = Tbl::factory('divisions')
			->where('id', '=', $this->item->division_id)
			->read(1);

		// Database transaction start
		Database::instance()->begin();

		// Try
		try
		{
			/**
			 * Delete
			 */
			// Delete items categories カテゴリーの削除
			$used_category_ids = Tbl::factory('items_categories')
				->where('item_id', '=', $this->item->id)
				->read()
				->as_array(NULL, 'id');

			if ($used_category_ids)
			{
				foreach ($used_category_ids as $used_category_id)
				{
					Tbl::factory('items_categories')
						->get($used_category_id)
						->delete();
				}
			}

			// Delete items fields フィールドの削除
			$used_field_ids = Tbl::factory('items_fields')
				->where('item_id', '=', $this->item->id)
				->read()
				->as_array(NULL, 'id');

			if ($used_field_ids)
			{
				foreach ($used_field_ids as $used_field_id)
				{
					Tbl::factory('items_fields')
						->get($used_field_id)
						->delete();
				}
			}

			// Delete items tags タグの削除
			$used_tag_ids = Tbl::factory('items_tags')
				->where('item_id', '=', $this->item->id)
				->read()
				->as_array(NULL, 'id');

			if ($used_tag_ids)
			{
				foreach ($used_tag_ids as $used_tag_id)
				{
					Tbl::factory('items_tags')
						->get($used_tag_id)
						->delete();
				}
			}

			// Delete item comments コメントの削除
			$used_comment_ids = Tbl::factory('received_comments')
				->where('item_id', '=', $this->item->id)
				->read()
				->as_array(NULL, 'id');

			if ($used_comment_ids)
			{
				foreach ($used_comment_ids as $used_comment_id)
				{
					Tbl::factory('received_comments')
						->get($used_comment_id)
						->delete();
				}
			}

			// Delete images イメージの削除
			$used_image_ids = Tbl::factory('images')
				->where('item_id', '=', $this->item->id)
				->read()
				->as_array(NULL, 'id');

			if ($used_image_ids)
			{
				foreach ($used_image_ids as $used_image_id)
				{
					Tbl::factory('images')
						->get($used_image_id)
						->delete();
				}
			}

			// Delete image files and directory イメージファイルとディレクトリの削除
			Cms_Helper::delete_dir($this->item->segment, $this->settings->image_dir.'/item/'.$division->segment, TRUE);

			// Delete file
			Cms_Helper::delete_file($this->item->segment, $this->settings->item_dir.'/'.$division->segment);

			// Delete
			$this->item->delete();

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
				Notice::ERROR//, $e->getMessage()
			);
		}

		/**
		 * Redirect to wrapper edit
		 */
		$this->redirect(URL::site("{$this->settings->backend_name}/items/{$division->segment}", 'http'));
	}

	/**
	 * Action edit
	 */
	public function action_edit()
	{
		/**
		 * Get item etc
		 */
		// <editor-fold defaultstate="collapsed" desc="Get item etc">
		// Get division
		$division = Tbl::factory('divisions')
			->where('id', '=', $this->item->division_id)
			->read(1);

		// Direct set to division
		$this->item->division_segment = $division->segment;
		$this->item->division_name = $division->name;
		$this->item->issued = $this->item->issued ? Date::formatted_time($this->item->issued, 'Y-n-j h:i') : $this->item->issued;
		$this->item->created = $this->item->created ? Date::formatted_time($this->item->created, 'Y-n-j h:i') : $this->item->created;
		$this->item->send_comment_is_on = $this->item->send_comment_is_on ? : 0;

		// Get content from file and direct set to $this->item
		$this->item->delete_url = URL::site("{$this->settings->backend_name}/items/{$division->segment}/delete/{$this->item->id}", 'http');

		// Save old file
		$oldname = $this->item->segment;

		// Get item category_ids
		$item_category_ids = Tbl::factory('items_categories')
			->where('item_id', '=', $this->item->id)
			->read()
			->as_array(NULL, 'category_id');

		$this->item->category_ids = implode(', ', $item_category_ids);

		// Get categories
		$categories = Tbl::factory('categories')
			->where('division_id', '=', $this->item->division_id)
			->read()
			->as_array();

		// Get item tag_ids
		$item_tag_ids = Tbl::factory('items_tags')
			->where('item_id', '=', $this->item->id)
			->read()
			->as_array(NULL, 'tag_id');

		$this->item->tag_ids = implode(', ', $item_tag_ids);

		// Get tags
		$tags = Tbl::factory('tags')
			->read()
			->as_array();

		// Get divisions
		$divisions = Tbl::factory('divisions')
			->read()
			->as_array();

		// Get parents
		$parents = Tbl::factory('items')
			->read()
			->as_array();

		// </editor-fold>

		/**
		 * If update
		 */
		// <editor-fold defaultstate="collapsed" desc="If update">
		if ($this->request->post('update'))
		{
			// Set post to item
			$this->item->segment = Arr::get($this->request->post(), 'segment');
			$this->item->title = Arr::get($this->request->post(), 'title');
			$this->item->catch = Arr::get($this->request->post(), 'catch');
			$this->item->keywords = Arr::get($this->request->post(), 'keywords');
			$this->item->description = Arr::get($this->request->post(), 'description');
			$this->item->summary = Arr::get($this->request->post(), 'summary');
			$this->item->order = Arr::get($this->request->post(), 'order');
			$this->item->is_active = Arr::get($this->request->post(), 'is_active', 0);
			$this->item->issued = Arr::get($this->request->post(), 'issued', $this->item->created);
			$this->item->category_ids = $this->request->post('category_id') ? implode(', ', $this->request->post('category_id')) : '[]';
			$this->item->tag_ids = $this->request->post('tag_id') ? implode(', ', $this->request->post('tag_id')) : '[]';
			$this->item->parent_id = Arr::get($this->request->post(), 'parent_id');
			$this->item->send_comment_is_on = Arr::get($this->request->post(), 'send_comment_is_on', 0);

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				// Update item
				Tbl::factory('items')
					->get($this->item->id)
					->update(array(
						'segment' => Arr::get($this->request->post(), 'segment'),
						'title' => Arr::get($this->request->post(), 'title'),
						'catch' => Arr::get($this->request->post(), 'catch'),
						'keywords' => Arr::get($this->request->post(), 'keywords'),
						'description' => Arr::get($this->request->post(), 'description'),
						'summary' => Arr::get($this->request->post(), 'summary'),
						'order' => Arr::get($this->request->post(), 'order'),
						'is_active' => Arr::get($this->request->post(), 'is_active', 0),
						'issued' => Arr::get($this->request->post(), 'issued', $this->item->created),
						'parent_id' => Arr::get($this->request->post(), 'parent_id'),
						'send_comment_is_on' => Arr::get($this->request->post(), 'send_comment_is_on', 0),
				));

				// Delete category カテゴリーをいったん削除
				$delete_category_ids = Tbl::factory('items_categories')
					->where('item_id', '=', $this->item->id)
					->read()
					->as_array(NULL, 'id');

				foreach ($delete_category_ids as $delete_category_id)
				{
					Tbl::factory('items_categories')
						->get($delete_category_id)
						->delete();
				}

				// Create category 新たにカテゴリーをつける
				if ($this->request->post('category_id'))
				{
					foreach ($this->request->post('category_id') as $category_id)
					{
						// リレーションテーブルなので念のためカテゴリーが有るかチェック
						$category_exist = (bool) Tbl::factory('categories')
								->where('id', '=', $category_id)
								->read('id');

						// なかったらエラー
						if (!$category_exist)
						{
							throw new Kohana_Exception(Kohana::message('general', 'not_exist'), array(':text' => 'Category'));
						}

						// Create items_categories
						Tbl::factory('items_categories')
							->create(array(
								'item_id' => $this->item->id,
								'category_id' => $category_id,
						));
					}
				}

				// Delete tag タグをいったん削除
				$delete_tag_ids = Tbl::factory('items_tags')
					->where('item_id', '=', $this->item->id)
					->read()
					->as_array(NULL, 'id');

				foreach ($delete_tag_ids as $delete_tag_id)
				{
					Tbl::factory('items_tags')
						->get($delete_tag_id)
						->delete();
				}

				// Create tag 新たにタブをつける
				if ($this->request->post('tag_id'))
				{
					foreach ($this->request->post('tag_id') as $tag_id)
					{
						// リレーションテーブルなので念のためカテゴリーが有るかチェック
						$tag_exist = (bool) Tbl::factory('tags')
								->where('id', '=', $tag_id)
								->read('id');

						// なかったらエラー
						if (!$tag_exist)
						{
							throw new Kohana_Exception(Kohana::message('general', 'not_exist'), array(':text' => 'Tag'));
						}

						// Create items_tags
						Tbl::factory('items_tags')
							->create(array(
								'item_id' => $this->item->id,
								'tag_id' => $tag_id,
						));
					}
				}

				// New file
				$newname = $this->item->segment;

				// rename file
				$rename_file_success = Cms_Helper::rename_file($oldname, $newname, $this->settings->item_dir.'/'.$division->segment);

				// images dir change name イメージディレクトリの名前変更
				if ($rename_file_success)
				{
					Cms_Helper::rename_dir($oldname, $newname, $this->settings->image_dir.'/item/'.$division->segment);
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
		// </editor-fold>

		/**
		 * View
		 */
		// <editor-fold defaultstate="collapsed" desc="View">
		$this->partials['local_menu'] = Tpl::get_file('local_menu', $this->settings->back_tpl_dir);

		$content_file = Tpl::get_file('edit', $this->settings->back_tpl_dir.'/items', $this->partials);

		$this->content = Tpl::factory($content_file)
			->set('item', $this->item)
			->set('divisions', $divisions)
			->set('categories', $categories)
			->set('tags', $tags)
			->set('parents', $parents);
		// </editor-fold>
	}

	/**
	 * Action content
	 */
	public function action_content()
	{
		/**
		 * Get item etc
		 */
		// <editor-fold defaultstate="collapsed" desc="Get item etc">
		// Get division
		$division = Tbl::factory('divisions')
			->where('id', '=', $this->item->division_id)
			->read(1);

		// Direct set to division
		$this->item->division_segment = $division->segment;
		$this->item->division_name = $division->name;

		// Get content from file and direct set to $this->item
		$this->item->content = Tpl::get_file($this->item->segment, $this->settings->item_dir.'/'.$division->segment);

		// Save present segment
		$oldfile = $this->item->segment;

		// Get divisions
		$divisions = Tbl::factory('divisions')
			->read()
			->as_array();

		// Get shapes
		$shapes = Cms_Helper::get_dirfiles('shape', $this->settings->front_tpl_dir . $this->settings->front_theme);
		// </editor-fold>

		/**
		 * If update
		 */
		// <editor-fold defaultstate="collapsed" desc="If update">
		if ($this->request->post('update'))
		{
			// Set post to division
			$this->item->shape_segment = $this->request->post('shape_segment');
			$this->item->content = $this->request->post('content');

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				// Update
				Tbl::factory('items')
					->get($this->item->id)
					->update(array(
						'shape_segment' => $this->request->post('shape_segment') ? : NULL,
				));

				// New file
				$newfile = $this->item->segment;

				// rename file
				Cms_Helper::rename_file($oldfile, $newfile, $this->settings->item_dir.'/'.$division->segment);

				// Update file
				Cms_Helper::set_file($newfile, $this->settings->item_dir.'/'.$division->segment, $this->request->post('content'));

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
		// </editor-fold>

		/**
		 * View
		 */
		// <editor-fold defaultstate="collapsed" desc="View">
		$this->partials['local_menu'] = Tpl::get_file('local_menu', $this->settings->back_tpl_dir);

		$content_file = Tpl::get_file('content', $this->settings->back_tpl_dir.'/items', $this->partials);

		$this->content = Tpl::factory($content_file)
			->set('item', $this->item)
			->set('divisions', $divisions)
			->set('shapes', $shapes)
			->set('post', $this->request->post());
		// </editor-fold>
	}

	/**
	 * Action images
	 */
	public function action_images()
	{
		/*
		 * Build columns
		 */
		// <editor-fold defaultstate="collapsed" desc=" Build columns">
		// Get order
		$query = $this->request->query();
		$order_column = Arr::get($query, 'order_column', 'order');
		$order_direction = Arr::get($query, 'order_direction', 'ASC');

		// Build columns
		$columns = array(
			array(
				'name' => 'id',
				'order_column' => 'id',
				'order_direction' => 'ASC',
			),
			array(
				'name' => 'segment',
				'order_column' => 'segment',
				'order_direction' => 'ASC',
			),
			array(
				'name' => 'name',
				'order_column' => 'name',
				'order_direction' => 'ASC',
			),
			array(
				'name' => 'order',
				'order_column' => 'order',
				'order_direction' => 'ASC',
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
		 * Get division
		 */
		$division = Tbl::factory('divisions')
			->where('id', '=', $this->item->division_id)
			->read(1);

		/*
		 * If create
		 */
		// <editor-fold defaultstate="collapsed" desc="If create">
		$create = array();

		if ($this->request->post('create'))
		{
			// Build data
			$create['item_id'] = $this->item->id;
			$create['segment'] = $this->request->post('create_segment');
			$create['name'] = $this->request->post('create_name');
			$create['description'] = $this->request->post('create_description');
			$create['order'] = $this->request->post('create_order');
			$create['image_file'] = $_FILES['create_image_file'];

			// Get image type
			switch ($create['image_file']['type'])
			{
				case 'image/jpeg':
					$ext = '.jpg';
					break;

				case 'image/png':
					$ext = '.png';
					break;

				case 'image/gif':
					$ext = '.gif';
					break;

				default:
					$ext = NULL;
					break;
			}

			$create['ext'] = $ext;

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				// Create
				Tbl::factory('images')
					->create($create, 'validate_upload');

				// Image division directory // イメージを入れるディレクトリ
				$dir_path = 'application/'.$this->settings->image_dir.'/item/'.$division->segment.'/'.$this->item->segment.'/';

				// Upload image イメージをアップロード
				$filename = Upload::save($create['image_file'], $create['segment'].$ext, $dir_path);

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
						->save($dir_path.$create['segment'].$key.$ext);
				}

				// Database commit
				Database::instance()->commit();

				// Clear create
				$create['segment'] = NULL;
				$create['name'] = NULL;
				$create['description'] = NULL;
				$create['order'] = NULL;

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
					Notice::ERROR, $e->getMessage()//, NULL, array($e->getFile(), $e->getLine())
				);
			}
		}
		// </editor-fold>

		/*
		 * If delete
		 */
		// <editor-fold defaultstate="collapsed" desc="If delete">
		if ($this->request->post('delete'))
		{
			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				// Get delete image ids
				$delete_image_ids = Arr::get($this->request->post(), 'delete_image_id', array());

				// Iterate and chack and delete
				foreach ($delete_image_ids as $delete_image_id)
				{
					// Get image
					$delete_image = Tbl::factory('images')->get($delete_image_id);

					// If image is used image for main
					if ($this->item->image_id == $delete_image->id)
					{
						// Add warning notice
						throw new Warning_Exception(Kohana::message('general', 'image_is_used_for_main'), array(':id' => $delete_image_id));
					}

					// logged in userのroleとuser_idをチェックして、editの場合user_idが一致しないと404
					if ($this->logged_in_user->role == 'edit' AND ! ($this->logged_in_user->id == $this->item->user_id))
					{
						throw HTTP_Exception::factory(404);
					}

					// Delete
					$deleted_image = $delete_image->delete();

					// Get directory
					$dir_path = 'application/'.$this->settings->image_dir.'/item/'.$division->segment.'/'.$this->item->segment;

					// Delete image files
					unlink($dir_path.'/'.$deleted_image->segment.$deleted_image->ext);
					unlink($dir_path.'/'.$deleted_image->segment.'_v'.$deleted_image->ext);
					unlink($dir_path.'/'.$deleted_image->segment.'_h'.$deleted_image->ext);
					unlink($dir_path.'/'.$deleted_image->segment.'_s'.$deleted_image->ext);
				}

				// Database commit
				Database::instance()->commit();

				// Add success notice
				if ($delete_image_ids)
				{
					Notice::add(Notice::SUCCESS, Kohana::message('general', 'delete_success'));
				}
				else
				{
					Notice::add(Notice::SUCCESS, Kohana::message('general', 'no_delete'), array(':text' => 'image'));
				}
			}
			catch (HTTP_Exception_302 $e)
			{
				$this->redirect($e->location());
			}
			catch (Warning_Exception $e)
			{
				// Database rollback
				Database::instance()->rollback();

				// Add
				Notice::add(Notice::WARNING, $e->getMessage());
			}
			catch (Exception $e)
			{
				// Database rollback
				Database::instance()->rollback();

				// Add error notice
				Notice::add(
					Notice::ERROR, $e->getMessage().$e->getFile().$e->getLine()
				);
			}
		}
		// </editor-fold>

		/*
		 * Get images
		 */
		// <editor-fold defaultstate="collapsed" desc="Get images">
		$images = Tbl::factory('images')
			->where('item_id', '=', $this->item->id)
			->order_by($order_column, $order_direction)
			->read()
			->as_array();
		// </editor-fold>

		/*
		 * If update
		 */
		// <editor-fold defaultstate="collapsed" desc="If update">
		if ($this->request->post('update'))
		{
			$post = $this->request->post();
			$this->item->image_id = isset($post['image_id']) ? $post['image_id'] : NULL;

			// Rotate new images
			foreach ($_FILES['image_file'] as $key => $value)
			{
				foreach ($value as $k => $v)
				{
					$post['image_file'][$k][$key] = $v;
				}
			}

			// Set post to images
			foreach ($images as $image)
			{
				$image->old_segment = $image->segment;
				$image->old_ext = $image->ext;
				$image->segment = $post['segment'][$image->id];
				$image->name = $post['name'][$image->id];
				$image->description = $post['description'][$image->id];
				$image->order = $post['order'][$image->id];
				$image->image_file = $post['image_file'][$image->id];
			}

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				// Update items
				Tbl::factory('items')
					->get($this->item->id)
					->update(array('image_id' => $this->item->image_id));

				// Update
				foreach ($images as $image)
				{
					// Get directory
					$dir_path = 'application/'.$this->settings->image_dir.'/item/'.$division->segment.'/'.$this->item->segment.'/';

					// If image upload
					if (Upload::not_empty($image->image_file))
					{
						// Get image type
						switch ($image->image_file['type'])
						{
							case 'image/jpeg':
								$ext = '.jpg';
								break;

							case 'image/png':
								$ext = '.png';
								break;

							case 'image/gif':
								$ext = '.gif';
								break;

							default:
								$ext = NULL;
								break;
						}

						$image->ext = $ext;

						// Updae images table
						Tbl::factory('images')
							->get($image->id)
							->update(array(
								'segment' => $image->segment,
								'ext' => $image->ext,
								'name' => $image->name,
								'description' => $image->description,
								'order' => $image->order,
								'image_file' => $image->image_file,
								), 'validate_upload');

						// Delete image file
						if (is_file($dir_path.$image->old_segment.$image->old_ext))
						{
							unlink($dir_path.$image->old_segment.$image->old_ext);
							unlink($dir_path.$image->old_segment.'_v'.$image->old_ext);
							unlink($dir_path.$image->old_segment.'_h'.$image->old_ext);
							unlink($dir_path.$image->old_segment.'_s'.$image->old_ext);
						}

						// Upload image
						$filename = Upload::save($image->image_file, $image->segment.$ext, $dir_path);

						// Build sizes
						$sizes = array(
							'_v' => explode(',', str_replace(' ', '', $this->settings->image_v)),
							'_h' => explode(',', str_replace(' ', '', $this->settings->image_h)),
							'_s' => explode(',', str_replace(' ', '', $this->settings->image_s)),
						);

						// Resize image
						foreach ($sizes as $key => $value)
						{
							Image::factory($filename)
								->resize($value[0], $value[1], Image::INVERSE)
								->crop($value[0], $value[1])
								->save($dir_path.$image->segment.$key.$ext);
						}
					}
					else
					{
						Tbl::factory('images')
							->get($image->id)
							->update(array(
								'segment' => $image->segment,
								'name' => $image->name,
								'description' => $image->description,
								'order' => $image->order,
						));

						// Rename
						if (is_file($dir_path.$image->old_segment.$image->old_ext))
						{
							rename($dir_path.$image->old_segment.$image->old_ext, $dir_path.$image->segment.$image->ext);
							rename($dir_path.$image->old_segment.'_v'.$image->old_ext, $dir_path.$image->segment.'_v'.$image->ext);
							rename($dir_path.$image->old_segment.'_h'.$image->old_ext, $dir_path.$image->segment.'_h'.$image->ext);
							rename($dir_path.$image->old_segment.'_s'.$image->old_ext, $dir_path.$image->segment.'_s'.$image->ext);
						}
					}
				}

				// Database commit
				Database::instance()->commit();

				// Add success notice
				Notice::add(Notice::SUCCESS, Kohana::message('general', 'update_success'));

				// Reload
				$images = Tbl::factory('images')
					->where('item_id', '=', $this->item->id)
					->order_by('order')
					->read()
					->as_array();
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
					Notice::ERROR, $e->getMessage()//, NULL, array($e->getFile(), $e->getLine())
				);
			}
		}
		// </editor-fold>

		/**
		 * Set to images
		 */
		foreach ($images as $image)
		{
			$image->path = URL::site("imagefly", 'http').'/item/'.$division->segment.'/'.$this->item->segment.'/';
			$image->file = '/'.$image->segment.$image->ext;
			$image->delete_url = URL::site("{$this->settings->backend_name}/items/{$division->segment}/image_delete/{$this->item->id}_{$image->id}", 'http');
		}

		/**
		 * View
		 */
		// <editor-fold defaultstate="collapsed" desc="View">
		$this->partials['local_menu'] = Tpl::get_file('local_menu', $this->settings->back_tpl_dir);

		$content_file = Tpl::get_file('images', $this->settings->back_tpl_dir.'/items', $this->partials);

		$this->content = Tpl::factory($content_file)
			->set('columns', $columns)
			->set('item', $this->item)
			->set('images', $images)
			->set('division', $division)
			->set('create', $create)
			->set('post', $this->request->post());
		// </editor-fold>
	}

	/**
	 * Action image delete
	 */
	public function action_image_delete()
	{
		// Auto render off
		$this->auto_render = FALSE;

		// Get ids, if When it is smaller than 2 then throw to 404
		$ids = explode('_', $this->request->param('key'));
		if (!(count($ids) == 2)) throw HTTP_Exception::factory(404);

		// idsをitem_idとimage_idに分ける
		list($item_id, $image_id) = $ids;

		// Get image, if there is nothing then throw to 404
		$image = Tbl::factory('images')->get($image_id);
		if (!$image) throw HTTP_Exception::factory(404);

		// Get item, if there is nothing then throw to 404
		$this->item = Tbl::factory('items')->get($item_id);
		if (!$this->item) throw HTTP_Exception::factory(404);

		// Get division
		$division = Tbl::factory('divisions')
			->where('id', '=', $this->item->division_id)
			->read(1);

		/**
		 * Check
		 */
		if ($this->item->image_id == $image->id)
		{
			// Add warning notice
			Notice::add(Notice::WARNING, Kohana::message('general', 'image_is_used_for_main'), array(':id' => $this->item->image_id));

			// Redirect to wrapper edit
			$this->redirect(URL::site("{$this->settings->backend_name}/items/{$division->segment}/images/{$this->item->id}", 'http'));
		}

		/**
		 * Delete
		 */
		// Database transaction start
		Database::instance()->begin();

		// Try
		try
		{
			// Get directory
			$dir_path = 'application/'.$this->settings->image_dir.'/item/'.$division->segment.'/'.$this->item->segment.'/';

			// Delete image files
			if (is_file($dir_path.$image->segment.$image->ext))
			{
				unlink($dir_path.$image->segment.$image->ext);
				unlink($dir_path.$image->segment.'_v'.$image->ext);
				unlink($dir_path.$image->segment.'_h'.$image->ext);
				unlink($dir_path.$image->segment.'_s'.$image->ext);
			}

			// Delete
			$image->delete();

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
		$this->redirect(URL::site("{$this->settings->backend_name}/items/{$division->segment}/images/{$this->item->id}", 'http'));
	}

	/**
	 * Action fields
	 */
	public function action_fields()
	{
		/*
		 * Build table header
		 */
		// <editor-fold defaultstate="collapsed" desc="Build table header">
		// Get order
		$query = $this->request->query();
		$order_column = Arr::get($query, 'order_column', 'order');
		$order_direction = Arr::get($query, 'order_direction', 'ASC');

		// Build columns
		$columns = array(
			array(
				'name' => 'id',
				'order_column' => 'id',
				'order_direction' => 'ASC',
			),
			array(
				'name' => 'segment',
				'order_column' => 'segment',
				'order_direction' => 'ASC',
			),
			array(
				'name' => 'name',
				'order_column' => 'name',
				'order_direction' => 'ASC',
			),
			array(
				'name' => 'order',
				'order_column' => 'order',
				'order_direction' => 'ASC',
			),
			array(
				'name' => 'value',
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

		/**
		 * Get division
		 */
		$division = Tbl::factory('divisions')
			->where('id', '=', $this->item->division_id)
			->read(1);

		// Direct set to division
		$this->item->division_segment = $division->segment;
		$this->item->division_name = $division->name;

		/**
		 * Get item fields
		 */
		// <editor-fold defaultstate="collapsed" desc="Get fields">
		$fields = Tbl::factory('items_fields')
			->select('items_fields.*')
			->select('fields.segment')
			->select('fields.name')
			->select('fields.order')
			->join('fields')->on('items_fields.field_id', '=', 'fields.id')
			->where('items_fields.item_id', '=', $this->item->id)
			->where('fields.division_id', '=', $this->item->division_id)
			->order_by($order_column, $order_direction)
			->read()
			->as_array();
		// </editor-fold>

		/**
		 * If post
		 */
		if ($this->request->post('update'))
		{
			$post = $this->request->post();

			// Set post to tag
			foreach ($fields as $field)
			{
				$field->value = $post['value'][$field->id];
			}

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				// Update
				foreach ($fields as $field)
				{
					Tbl::factory('items_fields')
						->get($field->id)
						->update(array('value' => $field->value));
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
		// <editor-fold defaultstate="collapsed" desc="View">
		$this->partials['local_menu'] = Tpl::get_file('local_menu', $this->settings->back_tpl_dir);

		$content_file = Tpl::get_file('fields', $this->settings->back_tpl_dir.'/items', $this->partials);

		$this->content = Tpl::factory($content_file)
			->set('columns', $columns)
			->set('item', $this->item)
			->set('fields', $fields);
		// </editor-fold>
	}

	/**
	 * Action received comments
	 */
	public function action_received_comments()
	{
		/*
		 * Build table header
		 */
		// <editor-fold defaultstate="collapsed" desc="Build table header">
		// Get order
		$query = $this->request->query();
		$order_column = Arr::get($query, 'order_column', 'created');
		$order_direction = Arr::get($query, 'order_direction', 'DESC');

		// Build columns
		$columns = array(
			array(
				'name' => 'id',
				'order_column' => 'id',
				'order_direction' => 'ASC',
			),
			array(
				'name' => 'user id',
				'order_column' => 'user_id',
				'order_direction' => 'ASC',
			),
			array(
				'name' => 'replay id',
				'order_column' => 'replay_id',
				'order_direction' => 'ASC',
			),
			array(
				'name' => 'display name',
				'order_column' => 'display_name',
				'order_direction' => 'ASC',
			),
			array(
				'name' => 'subject',
				'order_column' => 'subject',
				'order_direction' => 'ASC',
			),
			array(
				'name' => 'created',
				'order_column' => 'created',
				'order_direction' => 'ASC',
			),
			array(
				'name' => 'is accept',
				'order_column' => 'is_accept',
				'order_direction' => 'ASC',
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
		 * Get division
		 */
		// <editor-fold defaultstate="collapsed" desc="Get division">
		$division = Tbl::factory('divisions')
			->where('id', '=', $this->item->division_id)
			->read(1);

		// Direct set to division
		$this->item->division_segment = $division->segment;
		$this->item->division_name = $division->name;
		// </editor-fold>

		/*
		 * If delete
		 */
		// <editor-fold defaultstate="collapsed" desc="If delete">
		if ($this->request->post())
		{
			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				// Get delete image ids
				$delete_received_comment_ids = Arr::get($this->request->post(), 'delete_received_comment_id', array());

				// Iterate and chack and delete
				foreach ($delete_received_comment_ids as $delete_received_comment_id)
				{
					// Get image
					$received_comment = Tbl::factory('received_comments')->get($delete_received_comment_id);

					// logged in userのroleとuser_idをチェックして、editの場合user_idが一致しないと404
					if ($this->logged_in_user->role == 'edit' AND ! ($this->logged_in_user->id == $this->item->user_id))
					{
						throw HTTP_Exception::factory(404);
					}

					// Delete
					$received_comment->delete();
				}

				// Database commit
				Database::instance()->commit();

				// Add success notice
				if ($delete_received_comment_ids)
				{
					Notice::add(Notice::SUCCESS, Kohana::message('general', 'delete_success'));
				}
				else
				{
					Notice::add(Notice::SUCCESS, Kohana::message('general', 'no_delete'), array(':text' => 'image'));
				}
			}
			catch (HTTP_Exception_302 $e)
			{
				$this->redirect($e->location());
			}
			catch (Warning_Exception $e)
			{
				// Database rollback
				Database::instance()->rollback();

				// Add
				Notice::add(Notice::WARNING, $e->getMessage());
			}
			catch (Exception $e)
			{
				// Database rollback
				Database::instance()->rollback();

				// Add error notice
				Notice::add(
					Notice::ERROR, $e->getMessage().$e->getFile().$e->getLine()
				);
			}
		}
		// </editor-fold>

		/*
		 * Get received comments
		 */
		// <editor-fold defaultstate="collapsed" desc="Get received comments">
		// Get received_comments
		$all_received_comments = Tbl::factory('received_comments')
			->where('item_id', '=', $this->item->id)
			->order_by($order_column, $order_direction)
			->read()
			->as_array();

		$paginate = Pgn::factory(array(
				'total_items' => count($all_received_comments),
				'items_per_page' => $this->settings->paginate_items_per_page_for_received_comments,
				'follow' => $this->settings->paginate_items_follow_for_received_comments,
		));

		// Paginated items
		$received_comments = array_slice($all_received_comments, $paginate->offset, $paginate->items_per_page);

		foreach ($received_comments as $received_comment)
		{
			$received_comment->delete_url = URL::site("{$this->settings->backend_name}/items/{$division->segment}/received-comment-delete/{$this->item->id}-{$received_comment->id}", 'http').URL::query();
		}
		// </editor-fold>

		/*
		 * If update
		 */
		// <editor-fold defaultstate="collapsed" desc="If update">
		if ($this->request->post('update'))
		{
			$post = $this->request->post();

			// Set post to tag
			foreach ($received_comments as $received_comment)
			{
				$received_comment->is_accept = isset($post['is_accept'][$received_comment->id]) ? $post['is_accept'][$received_comment->id] : 0;
			}

			// Database transaction start
			Database::instance()->begin();

			// Try
			try
			{
				// Update
				foreach ($received_comments as $received_comment)
				{
					Tbl::factory('received_comments')
						->get($received_comment->id)
						->update(array(
							'is_accept' => isset($post['is_accept'][$received_comment->id]) ? $post['is_accept'][$received_comment->id] : 0,
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
		// </editor-fold>

		/**
		 * View
		 */
		// <editor-fold defaultstate="collapsed" desc="View">
		$this->partials['local_menu'] = Tpl::get_file('local_menu', $this->settings->back_tpl_dir);
		$this->partials['paginate'] = Tpl::get_file('paginate', $this->settings->back_tpl_dir);

		$content_file = Tpl::get_file('received_comments', $this->settings->back_tpl_dir.'/items', $this->partials);

		$this->content = Tpl::factory($content_file)
			->set('item', $this->item)
			->set('columns', $columns)
			->set('received_comments', $received_comments)
			->set('paginate', $paginate)
			->set('post', $this->request->post());
		// </editor-fold>
	}

	/**
	 * Action received comment delete
	 */
	public function action_received_comment_delete()
	{
		// Auto render off
		$this->auto_render = FALSE;

		// Get ids, if When it is smaller than 2 then throw to 404
		$ids = explode('_', $this->request->param('key'));
		if (!(count($ids) == 2)) throw HTTP_Exception::factory(404);

		// idsをitem_idとreceived_comment_idに分ける
		list($item_id, $received_comment_id) = $ids;

		// Get received_comment, if there is nothing then throw to 404
		$received_comment = Tbl::factory('received_comments')->get($received_comment_id);
		if (!$received_comment) throw HTTP_Exception::factory(404);

		// Get item, if there is nothing then throw to 404
		$this->item = Tbl::factory('items')->get($item_id);
		if (!$this->item) throw HTTP_Exception::factory(404);

		// Get division
		$division = Tbl::factory('divisions')
			->where('id', '=', $this->item->division_id)
			->read(1);

		// Database transaction start
		Database::instance()->begin();

		// Try
		try
		{
			// Delete
			$received_comment->delete();

			// Database commit
			Database::instance()->commit();

			// Add success notice
			Notice::add(Notice::SUCCESS, Kohana::message('general', 'delete_success'));

			// redirect
			$this->redirect(URL::site("{$this->settings->backend_name}/items/{$division->segment}/received_comments/{$this->item->id}", 'http'));
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

		// Redirect to received_comments edit
		$this->redirect(URL::site("{$this->settings->backend_name}/items/{$division->segment}/received_comments/{$this->item->id}", 'http').URL::query());
	}

	/**
	 * After
	 */
	public function after()
	{
		parent::after();
	}

//-----------------------------------------------------------------
	// Todo::0 html5weditorを入れる。fieldだけ？　textarea全部？　classでつけるかな。
	// Todo::0 fieldはselect,check,radio,textarea,imageをえらべるようにする？

}
