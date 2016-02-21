<?php

defined('SYSPATH') OR die('No direct script access.');

class Kohana_Cms_Functions {

// getはid = [1, 2, 4]などのstringを作って渡す
// callは変数がそのまま使える
	/**
	 * Get item
	 *
	 * {{~ get_item(segment, TRUE, TRUE, TRUE)}}
	 *
	 * @param string  $segment
	 * @param boolean $images
	 * @param boolean $fields
	 * @param boolean $comments
	 * @return object
	 *
	 * object {
	 * id => string
	 * division_id => string
	 * shape_segment => string
	 * image_id => int
	 * user_id => int
	 * parent_id => int
	 * segment => string
	 * title => string
	 * catch => string
	 * keywords => string
	 * description => string
	 * summary => string
	 * order => string
	 * is_active => string
	 * issued => time string
	 * created => time string
	 * send_comment_is_on => bool
	 * division => object {
	 * 		id => int
	 * 		wrapper_id => int
	 * 		segment => string
	 * 		name => string
	 * }
	 * wrapper => object {
	 * 		id => int
	 * 		segment => string
	 * 		name => string
	 * 		content_type => string
	 * }
	 * main_image => object {
	 * 		id => int
	 * 		item_id => int
	 * 		segment => string
	 * 		ext => string
	 * 		name => string
	 * 		description => string
	 * 		order => string
	 * 		 path => string
	 * 		file => string
	 * }
	 * categories => array (
	 *  0 => object {
	 * 			id => int
	 * 			division_id => int
	 * 			segment => string
	 * 			name => string
	 * 			description => string
	 * 			order => int
	 *  }
	 * 	1 => object {}
	 * 	2 => object {}
	 * 	3 => object {}....
	 * )
	 * tags => array (
	 *  0 => object {
	 * 		id => int
	 * 		segment => string
	 * 		name => string
	 * 		description => string
	 * 		order => string
	 * 	}
	 * 	1 => object {}
	 * 	2 => object {}
	 * 	3 => object {}....
	 * )
	 * user => object {
	 * 	email => string
	 * 	username => string(4) "kohx"
	 * }
	 * current => bool
	 * is_home => bool
	 * images => array (
	 *  "[segment]" => object {
	 * 		id => int
	 * 		item_id => string
	 * 		segment => string
	 * 		ext => string
	 * 		name => string
	 * 		description => string
	 * 		order => int
	 * 		path => string
	 * 		file => string
	 * 		main => bool FALSE
	 *  }
	 * 	"[segment]" => object {}
	 * 	"[segment]" => object {}
	 * 	"[segment]" => object {}....
	 * )
	 * fields => array
	 * comments => array
	 * content => string
	 * }
	 */
	public static function get_item($segment, $images = FALSE, $fields = FALSE, $comments = FALSE)
	{
		$sql = Tbl::factory('items')
				->where('segment', '=', $segment);

		// エディターから上位の時はすべて表示
		if (!(Auth::instance()->logged_in('direct') OR Auth::instance()->logged_in('admin') OR Auth::instance()->logged_in('edit')))
		{
			//アクティブのみを選択
			$sql->where('is_active', '=', 1);
		}

		$item = $sql->read(1);

		// If there is item itemがfalseじゃないとき
		if ($item)
		{
			// ディビジョンを取得
			$item->division = Tbl::factory('divisions')
					->where('id', '=', $item->division_id)
					->read(1);

			// ラッパーを取得
			$item->wrapper = Tbl::factory('wrappers')
					->where('id', '=', $item->division->wrapper_id)
					->read(1);

			// Get main_image
			$item->main_image = Tbl::factory('images')
					->where('id', '=', $item->image_id)
					->read(1);

			if ($item->main_image)
			{
				$item->main_image->path = URL::site("imagefly", 'http') . '/item/' . $item->division->segment . '/' . $item->segment . '/';
				$item->main_image->file = '/' . $item->main_image->segment . $item->main_image->ext;
			}

			// Categories
			$item->categories = Tbl::factory('categories')
					->select('categories.*')
					->join('items_categories')->on('categories.id', '=', 'items_categories.category_id')
					->where('items_categories.item_id', '=', $item->id)
					->read()
					->as_array();

			// Tags
			$item->tags = Tbl::factory('tags')
					->select('tags.*')
					->join('items_tags')->on('tags.id', '=', 'items_tags.tag_id')
					->where('items_tags.item_id', '=', $item->id)
					->read()
					->as_array();

			// Get user
			$item->user = Tbl::factory('users')
					->select('email')
					->select('username')
					->where('id', '=', $item->user_id)
					->read(1);

			// parentのcuurentがなくなってる！！！！ ので追加
			// 多分、get_children作った時に消えた。。。たぶんOK
			$current_item_parent_id = Tbl::factory('items')
					->where('segment', '=', Request::current()->param('segment'))
					->read('parent_id');

			// Get current 現在開かれているItemのidと一致する場合 -> Get each itemで使用！
			if ($item->segment == Request::current()->param('segment') OR $item->id == $current_item_parent_id)
			{
				$item->current = TRUE;
			}

			/*
			 * is系 現在のページをis_[segment]で探す
			 */
			$item->{'is_' . $item->segment} = TRUE;

			// if images
			if ($images)
			{
				// Get images
				$item->images = Tbl::factory('images')
						->where('item_id', '=', $item->id)
						->order_by('order')
						->read()
						->as_array('segment');

				if ($item->images)
				{
					foreach ($item->images as $image)
					{
						$image->path = URL::site("imagefly", 'http') . '/item/' . $item->division->segment . '/' . $item->segment . '/';
						$image->file = '/' . $image->segment . $image->ext;
						$image->main = ($item->image_id === $image->id) ? TRUE : FALSE;
					}
				}
			}

			// if comments
			if ($comments)
			{
				// Get comments
				$item->comments = Tbl::factory('received_comments')
						->select('received_comments.*')
						->select('users.username')
						->join('users')->on('received_comments.user_id', '=', 'users.id')
						->where('item_id', '=', $item->id)
						->read()
						->as_array();
			}

			// if fields
			if ($fields)
			{
				$item->fields = Tbl::factory('fields')
						->where('division_id', '=', $item->division_id)
						->read()
						->as_array('segment');

				// Get field value
				foreach ($item->fields as $field)
				{
					$field->value = Tbl::factory('items_fields')
							->where('item_id', '=', $item->id)
							->where('field_id', '=', $field->id)
							->read('value');
				}
			}
		}

		return $item;
	}

	/**
	 * Get items
	 *
	 * @param array $params
	 * 	array(
	 * 		'id' => array(1,2,3),
	 * 		'segment' => array(segment1,segment2),
	 * 		'division' => array(division_segment1),
	 * 		'user' => array(username1,username2,username3),
	 * 		'category' => array(category_segment1,category_segment2,category_segment3),
	 * 		'tag' => array(tag_segment1,tag_segment2,tag_segment3),
	 * 		'parrent_id' => array(parrent_id1,parrent_id2),
	 * 		'order_column' => 'order_column',
	 * 		'order_direction' => 'order_direction',
	 * 		'offset' => '2',
	 * 		'limit' => '5',
	 * 		'xn' => '4',
	 * 		'paginate' => 'true',
	 * 		'items_per_page' => 4,
	 * 		'follow' => 2,
	 * 		'flags' => array('images','fields','comments'),
	 * 		'get_one' => 'true'
	 * 	);
	 *
	 * 	get
	 * 		?issued=2013 2013-5 2013-5-5
	 * 		?page=1
	 *
	 * @return \stdClass
	 *
	 * 	{{item.total}} itemの数
	 * 	{{item.items}} itemの配列、keyはsegment、ループできる
	 * 	itemのitems配列のキーxxxがあった場合
	 * 	{{item.items.xxx.id}}
	 * 	{{item.items.xxx.segment}}
	 * 	{{item.items.xxx.division_id}}
	 * 	{{item.items.xxx}}
	 * 	{{item.items.xxx}}
	 * 	{{item.items.xxx}}
	 * 	{{item.items.count}}
	 * 	{{item.prev}}
	 * 	{{item.curr}}
	 * 	{{item.next}}
	 * 	{{item.paginate}}
	 *
	 * 		currentpage
	 * 		total_item
	 * 		items_per_page
	 * 		total_pages
	 * 		current_first_item
	 * 		current_last_item
	 * 		first_page, prev_page, next_page, last_page
	 * 			index
	 * 			url
	 * 			current
	 * 		offset
	 * 		limit
	 * 		pages
	 * 			index
	 * 			url
	 * 			current
	 * 		exist
	 * 		follow
	 * 		follow_pre
	 * 		follow_suf
	 * 		follow_pages
	 * 			index
	 * 			url
	 * 			current
	 */
	public static function get_items(array $params)
	{
		/**
		 * Build results
		 */
		$return = new stdClass();
		$return->total = NULL;
		$return->items = NULL;
		$return->prev = NULL;
		$return->curr = NULL;
		$return->next = NULL;
		$return->paginate = NULL;

		// Get param
		$id = Arr::get($params, 'id');
		$segment = Arr::get($params, 'segment');
		$division = Arr::get($params, 'division');
		$user = Arr::get($params, 'user');
		$category = Arr::get($params, 'category');
		$tag = Arr::get($params, 'tag');
		$parent_id = Arr::get($params, 'parent_id');
		$order_column = Arr::get($params, 'order_column');
		$order_direction = Arr::get($params, 'order_direction', 'ASC');
		$offset = Arr::get($params, 'offset');
		$limit = Arr::get($params, 'limit');

		$paginate = strtolower(Arr::get($params, 'paginate')) == 'true' ? TRUE : FALSE;
		$items_per_page = Arr::get($params, 'items_per_page');
		$follow = Arr::get($params, 'follow');

		$xn = Arr::get($params, 'xn', 2);

		$flags = Arr::get($params, 'flags', array());
		$images_flag = in_array('images', $flags);
		$fields_flag = in_array('fields', $flags);
		$comments_flag = in_array('comments', $flags);

		$get_one = strtolower(Arr::get($params, 'get_one')) == 'true' ? TRUE : FALSE;

		// カレンダーとかの日付でフィルタするときに使う　?issued = 2013-7-7 or ?issued = 2013-7 or ?issued = 2013
		$issued = Request::current()->query('issued');

		/**
		 * Get items id and segment：パラメータからsqlを作って実行、キーはsegmentでidとsegmentを取得
		 */
		// <editor-fold defaultstate="collapsed" desc="Get items id and segment">
		$sql = Tbl::factory('items')
						->select('items.id')
						->select('items.segment')
						->join('divisions')->on('items.division_id', '=', 'divisions.id')
						->join('items_categories', 'LEFT')->on('items.id', '=', 'items_categories.item_id')
						->join('categories', 'LEFT')->on('items_categories.category_id', '=', 'categories.id')
						->join('items_tags', 'LEFT')->on('items.id', '=', 'items_tags.item_id')
						->join('tags', 'LEFT')->on('items_tags.tag_id', '=', 'tags.id')
						->join('users', 'LEFT')->on('items.user_id', '=', 'users.id');

		// エディターから上位の時はすべて表示
		if (!(Auth::instance()->logged_in('direct') OR Auth::instance()->logged_in('admin') OR Auth::instance()->logged_in('edit')))
		{
			//アクティブのみを選択
			$sql->where('is_active', '=', 1);
		}

		if ($id)
			$sql->where('items.id', 'IN', $id);

		if ($segment)
			$sql->where('items.segment', 'IN', $segment);

		if ($division)
			$sql->where('divisions.segment', 'IN', $division);

		if ($user)
			$sql->where('users.username', 'IN', $user);

		if ($category)
			$sql->where('categories.segment', 'IN', $category);

		if ($tag)
			$sql->where('tags.segment', 'IN', $tag);

		if ($parent_id)
			$sql->where('items.parent_id', 'IN', $parent_id);

		// カレンダーとかの日付でフィルタするときに使う　?issued = 2013-7-7 or ?issued = 2013-7 or ?issued = 2013
		if ($issued)
		{
			$strings = explode('-', $issued);
			$count = count($strings);

			if ($count == 1)
			{
				$start = Date::formatted_time("{$strings[0]}-01-01");
				$end = Date::formatted_time("{$strings[0]}-01-01 +1year -1day");
			}
			elseif ($count == 2)
			{
				$start = Date::formatted_time("{$strings[0]}-{$strings[1]}-01");
				$end = Date::formatted_time("{$strings[0]}-{$strings[1]}-01 +1month -1day");
			}
			elseif ($count == 3)
			{
				$start = Date::formatted_time("{$strings[0]}-{$strings[1]}-{$strings[2]}");
				$end = Date::formatted_time("{$strings[0]}-{$strings[1]}-{$strings[2]} +1day -1sec");
			}

			$sql->where('items.issued', '>=', $start);
			$sql->where('items.issued', '<=', $end);
		}

		// バックエンドの時はissueが来てなくても表示
		if (Request::current()->controller() !== 'Backend')
		{
			$sql->where('items.issued', '<=', Date::formatted_time('now'));
		}

		$sql->group_by('items.id');

		// if there is order_column
		if ($order_column)
		{
			$sql->order_by('items.' . $order_column, $order_direction);
		}

		// if there is offset
		if ($offset)
		{
			$sql->offset($offset);
		}

		// if there is limit
		if ($limit)
		{
			$sql->limit($limit);
		}

		// Items sqlを実行
		$items = $sql->read()->as_array('segment');

		// count, xn
		$c = 0;
		foreach ($items as &$item)
		{
			// countの追加
			$item->count = ++$c;
			$item->xn_start = (($item->count % $xn) == 1) ? TRUE : FALSE;
			$item->xn_end = (($item->count % $xn) == 0) ? TRUE : FALSE;
		}

		// </editor-fold>

		/**
		 * Get total items：トータルを追加
		 */
		// <editor-fold defaultstate="collapsed" desc="Get total items">
		$return->total = count($items);
		// </editor-fold>

		/**
		 * Pagenate：ページネートを作成、itemsをフィルター
		 */
		// <editor-fold defaultstate="collapsed" desc="Pagenate">
		if ($paginate)
		{
			// Paginate
			$paginate = Pgn::factory(array(
						'total_items' => $return->total,
						'items_per_page' => $items_per_page,
						'follow' => $follow,
			));

			// Paginated items
			$items = array_slice($items, $paginate->offset, $paginate->items_per_page);

			// Set return
			$return->paginate = $paginate;

			// paginate number
			$return->{'paginate' . $paginate->current_page} = TRUE;
		}
		// </editor-fold>

		/**
		 * Get each item：itemの内容を取得して、現在開かれてるitemのcurrentをTRUEにする
		 */
		// <editor-fold defaultstate="collapsed" desc="Get each item">
		// 現在開かれているitemを取得
		$current_item = Tbl::factory('items')
				->select('id')
				->select('parent_id')
				->where('segment', '=', Request::current()->param('segment'))
				->read(1);

		// Todo:: これカレントがないときエラーにならないように作っとく、これでよい？
		if (!$current_item)
		{
			$current_item = new stdClass();
			$current_item->id = false;
			$current_item->parent_id = false;
		}

		// itemsが０じゃないとき
		if ($items)
		{
			// itemの内容を取得
			foreach ($items as $key => &$item)
			{
				// cms item の get item でそれぞれのitemを取得
				$item_details = self::get_item($item->segment, $images_flag, $fields_flag, $comments_flag);

				// itemとitem_detailsをマージ
				$item = (object) array_merge((array) $item, (array) $item_details);

				// 現在開かれているItemのidかparent_idと一致する場合
				if ($item->id == $current_item->id OR $item->id == $current_item->parent_id)
				{
					$item->current = TRUE;
				}
			}
		}

		// Set return Itemを追加
		$return->items = $items;
		// </editor-fold>

		/**
		 * Get prev next：itemのページでget_imagesで取得したなかのprevとnextを取得する
		 */
		// <editor-fold defaultstate="collapsed" desc="Get prev next">
		$temp_items = array_values($items);

		foreach ($temp_items as $key => $value)
		{
			if ($value->id == $current_item->id)
			{
				if (isset($temp_items[$key - 1]))
					$return->prev = $temp_items[$key - 1];
				if (isset($temp_items[$key]))
					$return->curr = $value;
				if (isset($temp_items[$key + 1]))
					$return->next = $temp_items[$key + 1];
			}
		}
// </editor-fold>

		/**
		 * Return get_oneの時は一個だけとる
		 *
		 * Todo:: これでOK?
		 * 削除
		 */
		return $get_one == 1 ? reset($return->items) : $return;
//		return $return;
	}

	// <editor-fold defaultstate="collapsed" desc="削除">
	/**
	 * Call items
	 *
	 * {{%same_cat_items = get_items(
	 * 		id = [1, 2, 4],
	 * 		segment = [home, about, kohei],
	 * 		division = [page],
	 * 		category = [dog, cat],
	 * 		tag = [japan],
	 *
	 * 		order_column = issued,
	 * 		order_direction = ASC,
	 * 		items_per_page = 8,
	 * 		follow = 2,
	 *
	 * 		flags = [images, fields, comments]
	 * )}}
	 *
	 * ?issued=2013 2013-5 2013-5-5
	 * ?page=1
	 *
	 * @param string $param_string
	 * @return get_items
	 */
//	public static function call_items($param_string)
//	{
//		/**
//		 * Params パラメータを分解
//		 */
//		$param_string = str_replace(' ', '', $param_string);
//		preg_match_all("/\[(.[^\[\]]*)\]/", $param_string, $matches, PREG_SET_ORDER);
//
//		$replacement = array();
//		foreach ($matches as $matche)
//		{
//			$replacement[$matche[0]] = str_replace(',', '|', $matche[0]);
//		}
//
//		$param_strings = explode(',', str_replace(array_keys($replacement), array_values($replacement), $param_string));
//
//		$params = array();
//		foreach ($param_strings as $param_string)
//		{
//			list($key, $value) = explode('=', $param_string);
//
//			if (strpos($value, '[') !== FALSE)
//			{
//				$value_string = str_replace(array('[', ']'), '', $value);
//				$params[$key] = $value_string ? explode('|', $value_string) : array();
//			}
//			elseif (is_numeric($value))
//			{
//				$params[$key] = (integer) $value;
//			}
//			else
//			{
//				$params[$key] = $value;
//			}
//		}
//
//		return self::get_items($params);
//	}
	// </editor-fold>
	/**
	 * Get children
	 *
	 * {{%same_cat_items = shops.segment, 'id', 'DESC', NULL, NULL, ['images', 'fields'])}}
	 *
	 * @return get_items
	 */
	public static function get_children($parent_segment, $order_column = NULL, $order_direction = NULL, $offset = NULL, $limit = NULL, $flags = array())
	{
		// Todo::1 要らなくなる?
		// Todo::1 paramを１つの配列にする?
		// parent_segmentからparent_idを取得
		$parent_id = Tbl::factory('items')
				->where('segment', '=', $parent_segment)
				->read('id');

		// params array作成
		$params = array(
			'parent_id' => array($parent_id),
			'order_column' => $order_column,
			'order_direction' => $order_direction,
			'offset' => $offset,
			'limit' => $limit,
			'flags' => $flags,
		);

		// get_itemsをリターン
		return self::get_items($params);
	}

	public static function image_url($image, $width = '', $height = '', $type = 'o')
	{
		if (is_array($image))
			$image = (object) $image;

		if (isset($image) AND isset($image->path) AND isset($image->file))
		{
			return "{$image->path}w{$width}-h{$height}-{$type}{$image->file}";
		}
		else
		{
			return '';
		}
	}

	/**
	 * image_pick Todo::!!!
	 *
	 * {{~myimage = image_pick(item.images, 'aaaaa')}}
	 * {{~myimage = image_pick(item.images, ['aaa', 'bbb', 'ccc'])}}
	 * {{~myimage = image_pick(item.images, [1, 2, 3], 'id')}}
	 * {{~myimage = image_pick(item.images, true, 'main', ture)}}
	 *
	 * @param array $images
	 * @param mix $values
	 * @param string $column
	 * @param bool $not
	 * @return array
	 */
	public static function image_pick($images, $values, $column = NULL, $not = FALSE)
	{
		$return = array();

		$column = (is_null($column)) ? 'segment' : $column;

		if (!is_array($values))
		{
			$values = array($values);
		}

		if (!is_null($not))
		{
			foreach ($images as $image)
			{
				if (isset($image->{$column}) AND in_array($image->{$column}, $values))
					$return[$image->segment] = $image;
			}
		}
		// notがtrueの時は$valiesで選んでないものを選択
		else
		{
			foreach ($images as $image)
			{
				if (isset($image->{$column}) AND ! in_array($image->{$column}, $values))
					$return[$image->segment] = $image;
			}
		}

		return $return;
	}

	/**
	 * image_filter
	 *
	 * {{~myimage = image_filter(item.images, 'segment = neko')}} と一致（メインを含めない）
	 * {{~myimage = image_filter(item.images, 'segment ^= neko')}} で始まる（メインを含めない）
	 * {{~myimage = image_filter(item.images, 'segment *= neko')}} を含む（メインを含めない）
	 * {{~myimage = image_filter(item.images, 'segment $= neko')}} で終わる（メインを含めない）
	 * {{~myimage = image_filter(item.images, 'id ^= 1')}} 1で始まる（メインを含めない）
	 * {{~myimage = image_filter(item.images, 'false')}} main画像を省く
	 * {{~myimage = image_filter(item.images, 'segment *= neko', 'false')}} 猫を含む（メインを省く）
	 *
	 * @param array $images
	 * @param $string $string
	 * @param bool $use_main
	 * @return type
	 */
	public static function image_filter($images, $string = NULL, $use_main = FALSE)
	{
		// リターンの配列宣言
		$return = array();

		// objectの場合配列に変換
		if (is_object($images))
		{
			$images = (array) $images;
		}

		// 中身もobjectの場合配列に変換、メインFALSEの時unset
		$main_flag = ($use_main == 'true') ? TRUE : FALSE;
		foreach ($images as &$image)
		{
			$image = (array) $image;
			if (isset($image['main']) AND ! $main_flag)
			{
				if ($image['main'])
				{
					unset($images[$image['segment']]);
				}
			}
		}
		// unset!
		unset($image);

		if ($string)
		{
			$equal_pos = strpos($string, '=');


			if (strpos($string, '^='))
			{
				$pos = strpos($string, '^=');
				$column = trim(substr($string, 0, $pos));
				$search_value = trim(substr($string, $pos + 2));

				foreach ($images as $image)
				{
					$column_value = isset($image[$column]) ? $image[$column] : NULL;
					$seach_pos = strpos($column_value, $search_value);
					if ($seach_pos === 0)
					{
						$return[$image['segment']] = $image;
					}
				}
			}
			elseif (strpos($string, '$='))
			{
				$pos = strpos($string, '$=');
				$column = trim(substr($string, 0, $pos));
				$search_value = trim(substr($string, $pos + 2));

				foreach ($images as $image)
				{
					$column_value = isset($image[$column]) ? $image[$column] : NULL;
					$seach_pos = strpos($column_value, $search_value);
					$seach_result = $seach_pos !== FALSE ? substr($column_value, $seach_pos) : '';
					if ($seach_result === $search_value)
					{
						$return[$image['segment']] = $image;
					}
				}
			}
			elseif (strpos($string, '*='))
			{
				$pos = strpos($string, '*=');
				$column = trim(substr($string, 0, $pos));
				$search_value = trim(substr($string, $pos + 2));
				foreach ($images as $image)
				{
					$column_value = isset($image[$column]) ? $image[$column] : NULL;
					$seach_pos = strpos($column_value, $search_value);
					if ($seach_pos !== FALSE)
					{
						$return[$image['segment']] = $image;
					}
				}
			}
			else
			{
				$return = $images;
			}
		}

		return $return;
	}

	/**
	 * Get user
	 *
	 * $is_blockがTRUEの時はblocl以外を取得
	 */
	public static function get_user($user_id, $is_block = FALSE)
	{
		$result = array();

		$user = Tbl::factory('users')
				->where('id', '=', $user_id)
				->read(1);

		if ($is_block)
		{
			if ($user->is_block)
			{
				return FALSE;
			}
		}

		if ($user)
		{
			$result = array(
				'id' => $user->id,
				'username' => $user->username,
				'email' => $user->email,
				'avatar' => array(),
				'detail' => array(),
			);

			if (!is_file('application/' . Cms_Helper::settings('image_dir') . '/user/' . $user->username . '/avatar' . $user->ext))
			{
				$result['avatar'] = FALSE;
			}
			else
			{
				$result['avatar'] = array(
					'path' => URL::site("imagefly", 'http') . '/user/' . $user->username . '/',
					'file' => '/' . 'avatar' . $user->ext,
				);
			}

			$result['detail'] = Tbl::factory('users_details')
					->join('details')->on('users_details.detail_id', '=', 'details.id')
					->select('users_details.*')
					->select('details.name')
					->select('details.segment')
					->where('users_details.user_id', '=', $user->id)
					->read()
					->as_array('segment');
		}

		return $result;
	}

	/**
	 * Get users
	 *
	 * $is_blockがTRUEの時はblocl以外を取得
	 */
	public static function get_users($is_block = FALSE, $order_column = NULL, $order_direction = NULL)
	{
		$sql = Tbl::factory('users');

		if ($is_block)
		{
			$sql->where('is_block', '!=', 1);
		}

		if ($order_column)
		{
			$sql->order_by($order_column, $order_direction);
		}

		$users = $sql->read()->as_array('id');

		$result = array();

		foreach ($users as $user)
		{
			self::get_user($user->id, $is_block);
			$result[$user->id] = self::get_user($user->id, $is_block);
		}

		return $result;
	}

	/**
	 * Get Comments
	 *
	 * @param array $params
	 * 	array(
	 * 		'item_segment' => 'item_segment',
	 * 		'order_column' => 'order_column',
	 * 		'order_direction' => 'order_direction',
	 * 		'offset' => '2',
	 * 		'limit' => '5',
	 * 	);
	 */
	public static function get_comments(array $params)
	{
		/**
		 * Build results
		 */
		$return = new stdClass();
		$return->total = NULL;
		$return->comments = NULL;

		// Get param
		$item_segment = Arr::get($params, 'item_segment');
		$order_column = Arr::get($params, 'order_column');
		$order_direction = Arr::get($params, 'order_direction');
		$offset = Arr::get($params, 'offset');
		$limit = Arr::get($params, 'limit');

		// parent_segmentからparent_idを取得
		$item_id = Tbl::factory('items')
				->where('segment', '=', $item_segment)
				->read('id');

		$sql = Tbl::factory('received_comments')
				->where('item_id', '=', $item_id)
				->where('is_accept', '=', 1);

		// if there is order_column
		if ($order_column)
		{
			$sql->order_by($order_column, $order_direction);
		}

		// if there is offset
		if ($offset)
		{
			$sql->offset($offset);
		}

		// if there is limit
		if ($limit)
		{
			$sql->limit($limit);
		}

		// Items sqlを実行
		$return->comments = $sql->read()->as_array();

		// count
		$return->total = count($return->comments);

		return $return;
	}

	public static function count($array)
	{
		if (is_array($array))
		{
			return count($array);
		}
		elseif (is_object($array))
		{
			return count((array) $array);
		}
	}

	public static function translate($string)
	{
		return __($string);
	}

	public static function __($str)
	{

		return __($str);
	}

	public static function strip_tags($string, $allowable_tags = NULL)
	{
		return strip_tags($string, $allowable_tags);
	}

	public static function auto_p($string, $br = TRUE)
	{
		return Text::auto_p($string, $br = TRUE);
	}

	public static function auto_link($string)
	{
		return Text::auto_link($string);
	}

	public static function auto_link_emails($string)
	{
		return Text::auto_link_emails($string);
	}

	public static function auto_link_urls($string)
	{
		return Text::auto_link_urls($string);
	}

	public static function limit_chars($string, $limit = 100, $end_char = NULL, $preserve_words = FALSE)
	{
		return Text::limit_chars($string, $limit, $end_char, $preserve_words);
	}

	public static function limit_words($string, $limit = 100, $end_char = NULL)
	{
		return Text::limit_chars($string, $limit, $end_char);
	}

	public static function date($timestamp_format, $timestamp = NULL)
	{
		if ($timestamp)
		{
			return date($timestamp_format, $timestamp);
		}
		else
		{
			return date($timestamp_format);
		}
	}

	public static function date_format($datetime_str = 'now', $timestamp_format = 'Y-m-d')
	{
		return Date::formatted_time($datetime_str, $timestamp_format);
	}

	public static function form_value($mix, $glue = ', ')
	{
		if (is_array($mix))
		{
			return 'data-value="' . implode(array_filter($mix, 'strlen'), $glue) . '"';
		}
		else
		{
			return 'data-value="' . $mix . '"';
		}
	}

	public static function redirect($url = NULL)
	{
		if ($url)
		{
			HTTP::redirect($url);
		}
		else
		{
			HTTP::redirect();
		}
	}

	public static function set($val)
	{
		return $val;
	}

	public static function htmlspecialchars($val)
	{
		return htmlspecialchars($val);
	}

	public static function is($val1, $op, $val2)
	{
		switch ($op)
		{
			case '==':
				return ($val1 == $val2);
			case '!=':
				return ($val1 != $val2);
			case '<':
				return ($val1 < $val2);
			case '<=':
				return ($val1 <= $val2);
			case '>':
				return ($val1 > $val2);
			case '>=':
				return ($val1 >= $val2);

			default:
				break;
		}
	}

	public static function implode($glue, $pieces = array())
	{
		if ($glue AND $pieces)
		{
			$result = implode($glue, $pieces);
		}
		else
		{
			$result = NULL;
		}

		return $result;
	}

	public static function explode($delimiter, $string)
	{
		if ($delimiter AND $string)
		{
			$result = explode($delimiter, $string);
		}
		else
		{
			$result = NULL;
		}

		return $result;
	}

	public static function debug($value)
	{
		return Debug::vars($value);
	}

	public static function get_feed($feed_url, $limit = 10)
	{
		$result = array();

		// Get file contents
		$feed_xml = @file_get_contents($feed_url);

		//if feed_xml
		if ($feed_xml)
		{
			$feed = simplexml_load_string($feed_xml, 'SimpleXMLElement', LIBXML_NOCDATA);

			// if feed is not false
			if ($feed !== FALSE)
			{
				$namespaces = $feed->getNamespaces(TRUE);

				if (isset($feed->item) AND $feed->item)
				{
					$feed_items = $feed->item;
				}
				elseif (isset($feed->entry) AND $feed->entry)
				{
					$feed_items = $feed->entry;
				}
				elseif (isset($feed->channel->item) AND $feed->channel->item)
				{
					$feed_items = $feed->channel->item;
				}

				// itelate feed_items
				$i = 0;
				foreach ($feed_items as $feed_item)
				{
					if ($limit > 0 AND $i++ === $limit)
						break;
					$item_fields = (array) $feed_item;

					// get namespaced tags
					foreach ($namespaces as $ns)
					{
						$item_fields += (array) $feed_item->children($ns);
					}

					// PRの削除
					if (strpos($item_fields['title'], 'PR:') === FALSE)
					{
						$date = '';
						if (isset($item_fields['pubDate']))
						{
							$date = date('Y-m-d H:i:s', strtotime($item_fields['pubDate']));
						}
						elseif (isset($item_fields['date']))
						{
							$date = date('Y-m-d H:i:s', strtotime($item_fields['date']));
						}
						elseif (isset($item_fields['updated']))
						{
							$date = date('Y-m-d H:i:s', strtotime($item_fields['updated']));
						}

						$link = '';
						if (is_string($item_fields['link']))
						{
							$link = $item_fields['link'];
						}
						elseif (is_object($item_fields['link']))
						{
							$link = (string) $item_fields['link']->attributes()->href;
						}
						elseif (is_array($item_fields['link']))
						{
							foreach ($item_fields['link'] as $child)
							{
								if ((string) $child->attributes()->rel == 'alternate')
								{
									$link = (string) $child->attributes()->href;
								}
							}
						}

						// 内容を取得
						$default_description = '';
						if (isset($item_fields['description']))
						{
							$default_description = $item_fields['description'];
						}
						elseif (isset($item_fields['content']))
						{
							$default_description = $item_fields['content'];
						}

						// imageの取得
						$feed_images = array();
						preg_match_all('/<img .*?src ?= ?[\'"]([^>]+)[\'"].*?>/i', $default_description, $images);
						foreach ($images[0] as $image)
						{
							preg_match('/<img.*?src=(["\'])(.+?)\1.*?>/i', $image, $src);
							$url = $src[2];
							if (strpos($url, 'agoda') === FALSE
									AND strpos($url, 'gif') === FALSE
									AND strpos($url, 'blogmura.com') === FALSE
									AND strpos($url, 'rssad.jp') === FALSE
									AND strpos($url, 'amazon.com') === FALSE
							)
							{
								$feed_images[] = $url;
							}
						}

						// タグの削除
						$description = str_replace(array("\r\n", "\r", "\n"), '', strip_tags($default_description));

						$result[] = array(
							'title' => $item_fields['title'],
							'description' => $description,
							'link' => $link,
							'date' => $date,
							'images' => $feed_images,
						);
					}
				}// itelate feed_items
			}// if feed is not false
		}//if feed_xml

		return $result;
	}

	public static function json_decode($json)
	{
		$str = str_replace(array("\r\n", "\n", "\r"), '', $json);
		return json_decode($str);
	}

	public static function row_to_array($str)
	{
		$result = array();
		if (is_string($str))
		{
			$rows = explode("\n", $str);
			if ($rows)
			{
				foreach ($rows as $row)
				{
					$key = trim(substr($row, 0, strpos($row, ':')));
					$value = trim(substr($row, strpos($row, ':') + 1));
					$result[] = array('key' => $key, 'value' => $value);
				}
			}
		}
		return $result;
	}

	public static function number_format($number, $decimals = 0, $dec_point = '.', $thousands_sep = ',')
	{
		return number_format($number, $decimals, $dec_point, $thousands_sep);
	}
	
	public static function replace($subject, $search = array('_', '-', '/'), $replace = ' ')
	{
		return str_replace($search, $replace, $subject);
	}
}
