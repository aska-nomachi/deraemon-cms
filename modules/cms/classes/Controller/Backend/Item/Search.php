<?php

defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Item_Search extends Controller_Backend_Template {

	/**
	 * Before
	 */
	public function before()
	{
		parent::before();

		// Local menus
		$this->local_menus = array(
			'index' => array('name' => 'index', 'url' => URL::site("{$this->settings->backend_name}/item-search", 'http')),
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
		 * Get order
		 */
		$string = Arr::get($this->request->query(), 'string', '');
		$and_or = Arr::get($this->request->query(), 'and_or', 'and');
		$divisions = Arr::get($this->request->query(), 'divisions', array());
		$categories = Arr::get($this->request->query(), 'categories', array());
		$tags = Arr::get($this->request->query(), 'tags', array());

		$order_column = Arr::get($this->request->query(), 'order_column', 'id');
		$order_direction = Arr::get($this->request->query(), 'order_direction', 'ASC');

		$get = array(
			'string' => $string,
			'and_or' => $and_or,
			'divisions' => $divisions,
			'categories' => $categories,
			'tags' => $tags,
			'order_column' => $order_column,
			'order_direction' => $order_direction,
		);

		/*
		 * Get lists
		 */
		$division_list = Tbl::factory('divisions')
			->read()
			->as_array();

		$category_list = Tbl::factory('categories')
			->read()
			->as_array();

		$tag_list = Tbl::factory('tags')
			->read()
			->as_array();

		/*
		 * Build columns
		 */
		// <editor-fold defaultstate="collapsed" desc="Build columns">
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
			'division' => array(
				'name' => 'division',
				'order_column' => 'division_name',
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

				$column['url'] = URL::base(TRUE).Request::current()->uri().URL::query(array('order_column' => $column['order_column'], 'order_direction' => $column['order_direction']), TRUE);
			}
		}
		// </editor-fold>

		/*
		 * Search items
		 */
		// <editor-fold defaultstate="collapsed" desc="Get items">
		$sql = DB::select('items.id', 'items.segment')
				->from('items')
				->select('items.*')
				->select('users.username')
				->select(array('divisions.segment', 'division_segment'))
				->select(array('divisions.name', 'division_name'))
				->join('users', 'LEFT')->on('items.user_id', '=', 'users.id')
				->join('divisions')->on('items.division_id', '=', 'divisions.id')
				->join('items_categories', 'LEFT')->on('items.id', '=', 'items_categories.item_id')
				->join('categories', 'LEFT')->on('items_categories.category_id', '=', 'categories.id')
				->join('items_tags', 'LEFT')->on('items.id', '=', 'items_tags.item_id')
				->join('tags', 'LEFT')->on('items_tags.tag_id', '=', 'tags.id');

		// authority is edit
		if ($this->logged_in_user->role == 'edit')
		{
			$sql->where('users.id', '=', $this->logged_in_user->id);
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

		// string タブスペースなんかを半角に置き換えてexplodeで分ける
		if ($string)
		{
			$strings = array_filter(explode(' ', preg_replace(array('/\s+/', '/,/', '/、/'), array(' ', ' ', ' '), mb_convert_kana($string, "s"))));

			// AND検索のとき
			if ($and_or == 'and')
			{
				$sql->where_open();
				foreach ($strings as $string)
				{
					$sql->and_where(DB::expr(
						"concat(ifnull(items.segment, ''), ' ', ifnull(items.title, ''), ' ', ifnull(items.catch, ''), ' ', ifnull(items.keywords, ''), ' ', ifnull(items.description, ''), ' ', ifnull(items.summary, ''))"), 'like', "%$string%");
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
		}

		$all_items = $sql->group_by('items.id')
			->order_by($order_column, $order_direction)
			->as_object()
			->execute()
			->as_array('segment');

		// Pagenate
		$pagenate = Pgn::factory(array(
				'total_items' => count($all_items),
				'items_per_page' => $this->settings->pagenate_items_per_page_for_items,
				'follow' => $this->settings->pagenate_items_follow_for_items,
		));

		// Paginated items
		$items = array_slice($all_items, $pagenate->offset, $pagenate->items_per_page);

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
			$item->summary = $item->summary;
			$item->edit_url = URL::site("{$this->settings->backend_name}/items/{$item->division_segment}/edit/{$item->id}", 'http');
		}
		// </editor-fold>

		/**
		 * View
		 */
		// <editor-fold defaultstate="collapsed" desc="View">
		$this->partials['pagenate'] = Tpl::get_file('pagenate', $this->settings->back_tpl_dir);

		$content_file = Tpl::get_file('index', $this->settings->back_tpl_dir.'/item_search', $this->partials);

		$this->content = Tpl::factory($content_file)
			->set('columns', $columns)
			->set('division_list', $division_list)
			->set('category_list', $category_list)
			->set('tag_list', $tag_list)
			->set('items', $items)
			->set('pagenate', $pagenate)
			->set('get', $get);
		// </editor-fold>
	}

	/**
	 * After
	 */
	public function after()
	{
		parent::after();
	}

}
